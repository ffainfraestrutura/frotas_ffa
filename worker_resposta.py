import time
import sys
import mysql.connector
import pywhatkit as kit
from datetime import datetime
import os
import pyautogui
import io
import re

config = {

    "user": "blvp",
    "password": "@F3r7n6c2",
    "host": "10.17.0.5",
    "database": "bdfrota",
    "autocommit": True,
}

# CONFIGURAÇÕES DE TEMPO - WORKER FROTAS
HORARIO_INICIO = 8  # Horário de início (8h)
HORARIO_FIM = 19   # Horário de fim (19h)
TEMPO_AGENDAMENTO_MINUTOS = 2  # +2 minutos para agendamento
TEMPO_WAIT_TIME = 40  # Tempo de carregamento do WhatsApp
TEMPO_POS_ENVIO = 30  # Aguarda após envio para garantir completude
TEMPO_INTERVALO_FINAL = 60  # Intervalo final entre envios
TEMPO_ERRO_PAUSA = 9  # Pausa em caso de erro
TEMPO_VERIFICACAO_FILA = 300  # 5 minutos = 300 segundos

def safe(text: str) -> str:
    enc = sys.stdout.encoding or "utf-8"

    return (text or "").encode(enc, errors="replace").decode(enc, errors="replace")

def log_to_file(message: str):
    """Salva mensagens relevantes em arquivo de log"""
    try:
        log_dir = "logs"
        if not os.path.exists(log_dir):
            os.makedirs(log_dir)
        timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        log_file = os.path.join(log_dir, "worker_frotas_resposta.log")
        with open(log_file, "a", encoding="utf-8") as f:
            f.write(f"[{timestamp}] {message}\n")
    except Exception as e:
        print(f"[ERRO] Falha ao escrever no log: {e}")

def verificar_data_anterior(resposta: str):
    """
    Verifica se há datas no formato dd/mm na resposta que sejam anteriores ao dia atual.
    Considera que datas em janeiro/fevereiro podem ser do próximo ano quando estamos em dezembro.
    Retorna (tem_data_anterior, datas_encontradas, data_hoje_str) ou (False, [], data_hoje_str).
    """
    try:
        # Busca padrões dd/mm na resposta
        padrao_data = r'\b(\d{1,2})/(\d{1,2})\b'
        matches = re.findall(padrao_data, resposta)
        
        hoje = datetime.now()
        data_hoje_str = hoje.strftime("%d/%m/%Y")
        
        if not matches:
            return False, [], data_hoje_str  # Não há datas na resposta
        
        ano_atual = hoje.year
        mes_atual = hoje.month
        datas_anteriores = []
        
        for dia_str, mes_str in matches:
            try:
                dia = int(dia_str)
                mes = int(mes_str)
                
                # Validar se dia e mês são válidos
                if not (1 <= dia <= 31 and 1 <= mes <= 12):
                    continue
                
                # LÓGICA AJUSTADA: Se estamos em novembro/dezembro e a data é janeiro/fevereiro/março
                # assume que é do próximo ano
                if mes_atual >= 11 and mes <= 3:
                    ano_da_data = ano_atual + 1
                else:
                    ano_da_data = ano_atual
                
                # Criar data com ano ajustado
                data_resposta = datetime(ano_da_data, mes, dia)
                
                # Se a data for anterior ao dia atual, adicionar à lista
                if data_resposta.date() < hoje.date():
                    data_formatada = data_resposta.strftime("%d/%m/%Y")
                    datas_anteriores.append(data_formatada)
                else:
                    # Log para debug: data considerada futura ou igual
                    print(f"[DEBUG] Data {dia:02d}/{mes:02d}/{ano_da_data} considerada futura ou igual a hoje")
                    
            except ValueError:
                # Data inválida, continua para próxima
                continue
        
        if datas_anteriores:
            return True, datas_anteriores, data_hoje_str
        else:
            return False, [], data_hoje_str
        
    except Exception as e:
        print(f"[ERRO] Falha ao verificar data na resposta: {e}")
        return False, [], datetime.now().strftime("%d/%m/%Y")
 
class Tee(io.TextIOBase):
    def __init__(self, *streams):
        self._streams = streams
    def write(self, s):
        for st in self._streams:
            try:
                st.write(s)
                st.flush()
            except Exception:
                pass
        return len(s)
    def flush(self):
        for st in self._streams:
            try:
                st.flush()
            except Exception:
                pass

# Garante que todo print/erro também vá para o arquivo de log existente
try:
    _tee_desktop_path = os.path.join(os.path.expanduser("~"), "Desktop")
    _tee_log_dir = os.path.join(_tee_desktop_path, "logs")
    os.makedirs(_tee_log_dir, exist_ok=True)
    _tee_log_path = os.path.join(_tee_log_dir, "worker_frotas_resposta.txt")
    _tee_fp = open(_tee_log_path, "a", encoding="utf-8")
    sys.stdout = Tee(sys.stdout, _tee_fp)
    sys.stderr = Tee(sys.stderr, _tee_fp)
except Exception:
    pass

def main():
    print("Worker Python Frota Ativo — monitorando novas respostas...\n")

    # Debug: lista diretórios e arquivos na pasta atual
    print("[INFO] Diretórios e arquivos na pasta atual:")
    import os
    for item in os.listdir('.'):
        print(" -", item)

    conn = mysql.connector.connect(**config)
    cursor = conn.cursor(dictionary=True)
    
    # Controle para evitar spam de mensagens de horário
    ultima_mensagem_horario = None
    mostrou_aviso_horario = False

    while True:
        try:
            # PRIMEIRA VERIFICAÇÃO: Horário permitido
            agora = datetime.now()
            hora_atual = agora.hour
            
            if hora_atual < HORARIO_INICIO or hora_atual >= HORARIO_FIM:
                # Controle para exibir mensagem apenas uma vez por período
                hora_chave = f"{hora_atual:02d}h"
                if ultima_mensagem_horario != hora_chave or not mostrou_aviso_horario:
                    print(f"[HIBERNAÇÃO] Fora do horário comercial ({hora_atual:02d}h) - Worker em modo hibernação")
                    if hora_atual < HORARIO_INICIO:
                        print(f"[INFO] Reativação programada para às {HORARIO_INICIO:02d}h00")
                    else:
                        print(f"[INFO] Reativação programada para amanhã às {HORARIO_INICIO:02d}h00")
                    log_to_file(f"[HIBERNAÇÃO] Worker hibernando - horário atual: {hora_atual:02d}h")
                    ultima_mensagem_horario = hora_chave
                    mostrou_aviso_horario = True
                
                # Em hibernação - pula toda verificação da fila
                time.sleep(TEMPO_VERIFICACAO_FILA)  # Aguarda tempo configurável antes de verificar novamente
                continue
            else:
                # Reset do controle quando entrar no horário permitido
                if mostrou_aviso_horario:
                    print(f"[REATIVAÇÃO] ✅ Worker reativado - Horário comercial ({hora_atual:02d}h) - Processando fila")
                    log_to_file(f"[REATIVAÇÃO] Worker ativo - horário comercial ({hora_atual:02d}h)")
                    mostrou_aviso_horario = False
            
            # SÓ CHEGA AQUI SE ESTIVER NO HORÁRIO PERMITIDO
            
            # Busca próximo registro pendente da fila
            cursor.execute("""
                SELECT id, idtbchamados, resposta, telefone
                FROM queue_ocorrencia_resposta
                WHERE processed_at IS NULL 
                AND telefone IS NOT NULL 
                AND telefone != ''
                ORDER BY id ASC
                LIMIT 1
            """)
            row = cursor.fetchone()

            if row:

                mensagem = (
                    f"Notificação de atualização em chamado na Frota!\n"
                    f"Houve uma nova resposta no sistema Frota referente ao chamado nº {row['idtbchamados']}:\n\n"
                    f"  {safe(row['resposta'])}\n\n"
                    f"Para mais detalhes, acesse seus chamados pelo link:\n"
                    f"https://ffasip.ddns.net:4242/frotas/chamados/listagemchamadoscliente.php\n\n"
                    f"Esta é uma mensagem automática de resposta ou fechamento de chamado— não é possível respondê-la e as atualizações não ocorrem em tempo real. "
                    f"Em caso de dúvidas, entre em contato com sua equipe na Frota ou verifique o chamado."
                )

                print(f"[EVENTO] Nova resposta para chamado {row['idtbchamados']}")
                log_to_file(f"[EVENTO] Nova resposta para chamado {row['idtbchamados']}")

                # Pega telefone da tabela
                telefone = row.get('telefone')
                

                # Verificação adicional de telefone válido
                if not telefone or telefone.strip() == '':
                    print(f"[AVISO] Telefone vazio/nulo para chamado {row['idtbchamados']} - marcando como processado")
                    log_to_file(f"[AVISO] Telefone vazio/nulo para chamado {row['idtbchamados']} - marcando como processado")
                    cursor.execute("UPDATE queue_ocorrencia_resposta SET processed_at = NOW() WHERE id = %s", (row["id"],))
                    conn.commit()
                    continue
                
                
                # Verificação adicional de resposta válida
                resposta = row.get('resposta')
                if not resposta or resposta.strip() == '':
                    print(f"[AVISO] Resposta vazia/nula para chamado {row['idtbchamados']} - marcando como processado")
                    log_to_file(f"[AVISO] Resposta vazia/nula para chamado {row['idtbchamados']} - marcando como processado")
                    cursor.execute("UPDATE queue_ocorrencia_resposta SET processed_at = NOW() WHERE id = %s", (row["id"],))
                    conn.commit()
                    continue
                
                # VERIFICAÇÃO: Resposta com apenas um caractere
                if len(resposta.strip()) <= 1:
                    print(f"[AVISO] Resposta com apenas um caractere para chamado {row['idtbchamados']} - marcando como processado")
                    log_to_file(f"[AVISO] Resposta com apenas um caractere para chamado {row['idtbchamados']} - marcando como processado")
                    log_to_file(f"[RESPOSTA] '{resposta}'")
                    cursor.execute("UPDATE queue_ocorrencia_resposta SET processed_at = NOW() WHERE id = %s", (row["id"],))
                    conn.commit()
                    continue
                
                # VERIFICAÇÃO: Frases desnecessárias (contém "duplicado")
                if "duplicado" in resposta.lower():
                    print(f"[AVISO] Resposta contém palavra 'duplicado' para chamado {row['idtbchamados']} - marcando como processado")
                    log_to_file(f"[AVISO] Resposta contém palavra 'duplicado' para chamado {row['idtbchamados']} - marcando como processado")
                    log_to_file(f"[RESPOSTA] {resposta}")
                    cursor.execute("UPDATE queue_ocorrencia_resposta SET processed_at = NOW() WHERE id = %s", (row["id"],))
                    conn.commit()
                    continue
                
                # NOVA VERIFICAÇÃO: Verificar se há datas anteriores na resposta
                tem_data_anterior, datas_encontradas, data_hoje = verificar_data_anterior(resposta)
                if tem_data_anterior:
                    datas_str = ", ".join(datas_encontradas)
                    print(f"[AVISO] Data anterior encontrada na resposta do chamado {row['idtbchamados']} - marcando como processado")
                    print(f"[INFO] Datas encontradas: {datas_str} | Data hoje: {data_hoje}")
                    log_to_file(f"[AVISO] Data anterior encontrada na resposta do chamado {row['idtbchamados']} - marcando como processado")
                    log_to_file(f"[INFO] Datas encontradas: {datas_str} | Data hoje: {data_hoje}")
                    log_to_file(f"[RESPOSTA] {resposta}")
                    cursor.execute("UPDATE queue_ocorrencia_resposta SET processed_at = NOW() WHERE id = %s", (row["id"],))
                    conn.commit()
                    continue
                
                # VERIFICAÇÃO DE DUPLICATAS: Verificar se já existe outro registro com mesmo idtbchamados e resposta
                cursor.execute("""
                    SELECT COUNT(*) as total
                    FROM queue_ocorrencia_resposta
                    WHERE idtbchamados = %s 
                    AND resposta = %s 
                    AND id != %s
                    AND processed_at IS NOT NULL
                """, (row['idtbchamados'], resposta, row['id']))
                duplicata_result = cursor.fetchone()
                
                if duplicata_result and duplicata_result['total'] > 0:
                    print(f"[AVISO] Duplicata encontrada para chamado {row['idtbchamados']} - mesmo chamado e resposta já processados anteriormente - marcando como processado")
                    log_to_file(f"[AVISO] Duplicata encontrada para chamado {row['idtbchamados']} - mesmo chamado e resposta já processados - marcando como processado")
                    log_to_file(f"[RESPOSTA DUPLICADA] {resposta}")
                    cursor.execute("UPDATE queue_ocorrencia_resposta SET processed_at = NOW() WHERE id = %s", (row["id"],))
                    conn.commit()
                    continue
                
                print(f"[INFO] Enviando para telefone: {telefone}")
                log_to_file(f"[INFO] Enviando mensagem para chamado {row['idtbchamados']} - telefone: {telefone}")

                # Envia WhatsApp para o telefone da tabela
                try:
                    # Normaliza telefone brasileiro para formato internacional
                    import re
                    digits = re.sub(r"\D", "", telefone)
                    if digits.startswith("55") and 12 <= len(digits) <= 13:
                        numero_formatado = f"+{digits}"
                    elif 10 <= len(digits) <= 11:
                        numero_formatado = f"+55{digits}"
                    else:
                        numero_formatado = f"+55{digits}"
                    
                    agora = datetime.now()
                    hora = agora.hour
                    # TEMPO CONFIGURÁVEL - FROTAS: Agendamento configurável
                    minuto = (agora.minute + TEMPO_AGENDAMENTO_MINUTOS) % 60  # Agendamento configurável
                    
                    # Ajusta hora se minuto passou de 59 (voltou para 0)
                    if minuto < agora.minute:
                        hora = (hora + 1) % 24
                    
                    # VERIFICAÇÃO ADICIONAL: Garantir que o horário de agendamento está dentro do permitido
                    if hora >= HORARIO_FIM:
                        print(f"[AVISO] Horário de agendamento ({hora:02d}:{minuto:02d}) ultrapassaria o limite ({HORARIO_FIM}h)")
                        print(f"[INFO] Mensagem para chamado {row['idtbchamados']} será reagendada para o próximo dia útil às {HORARIO_INICIO}h")
                        log_to_file(f"[AVISO] Horário de agendamento ultrapassaria {HORARIO_FIM}h - chamado {row['idtbchamados']} reagendado")
                        # NÃO marca como processado para tentar novamente no próximo ciclo
                        continue
                        
                    print(f"[INFO] Enviando para {numero_formatado} às {hora:02d}:{minuto:02d}")
                    
                    # Tenta enviar a mensagem (tempo maior para garantir carregamento completo)
                    kit.sendwhatmsg(numero_formatado, mensagem, hora, minuto, wait_time=TEMPO_WAIT_TIME, tab_close=False)

                    # Contagem e tentativas de Enter após o preenchimento da mensagem
                    print("[ATENÇÃO] Certifique-se de que o navegador com o WhatsApp Web está em primeiro plano!")
                    print("3")
                    time.sleep(1)
                    print("2")
                    time.sleep(1)
                    print("1")
                    time.sleep(1)
                    print("[ENVIO] Pressionando Enter (1ª tentativa)...")
                    pyautogui.press('enter')
                    time.sleep(5)  # tempo aumentado para 5 segundos
                    print("3")
                    time.sleep(1)
                    print("2")
                    time.sleep(1)
                    print("1")
                    time.sleep(1)
                    print("[ENVIO] Pressionando Enter (2ª tentativa)...")
                    pyautogui.press('enter')
                    

                    print(f"[OK] Mensagem agendada para: {numero_formatado}")
                    log_to_file(f"[OK] Mensagem agendada para chamado {row['idtbchamados']} - {numero_formatado} às {hora:02d}:{minuto:02d}")
                    

                    # TEMPO CONFIGURÁVEL - FROTAS: Verificação pós-envio (
                    # ajustável)
                    print(f"[INFO] Aguardando {TEMPO_POS_ENVIO} segundos para garantir que o envio seja completado...")
                    time.sleep(TEMPO_POS_ENVIO)  # Tempo configurável para garantir envio completo
                    
                    # Após o envio, fecha a aba do WhatsApp Web
                    try:
                        time.sleep(2)
                        import pygetwindow as gw
                        for w in gw.getAllTitles():
                            if 'WhatsApp' in w or 'whatsapp' in w.lower():
                                win = gw.getWindowsWithTitle(w)[0]
                                win.activate()
                                time.sleep(0.5)
                                pyautogui.hotkey('ctrl', 'w')
                                print('[INFO] Aba do WhatsApp Web fechada.')
                                break
                        else:
                            print('[AVISO] Não foi encontrada uma aba do WhatsApp Web para fechar.')
                    except Exception as e:
                        print(f'[AVISO] Não foi possível fechar a aba automaticamente: {e}')
                    
                    # SÓ marca como processado se chegou até aqui sem erro
                    cursor.execute("UPDATE queue_ocorrencia_resposta SET processed_at = NOW() WHERE id = %s", (row["id"],))
                    conn.commit()
                    
                    print(f"[OK] Registro {row['id']} marcado como processado")
                    log_to_file(f"[OK] Registro {row['id']} do chamado {row['idtbchamados']} marcado como processado")
                    
                    # TEMPO CONFIGURÁVEL - FROTAS: Intervalo entre envios (ajustável)
                    print(f"[INFO] Aguardando intervalo final de {TEMPO_INTERVALO_FINAL} segundos antes do próximo envio...")
                    total_tempo = TEMPO_WAIT_TIME + TEMPO_POS_ENVIO + TEMPO_INTERVALO_FINAL + 3  # +3 para outros processamentos
                    print(f"[INFO] (Tempo total desde início do envio: ~{total_tempo} segundos devido às verificações)")
                    time.sleep(TEMPO_INTERVALO_FINAL)  # Tempo configurável entre envios
                    
                    # Após processar um registro, volta ao loop para verificar se há mais na fila
                    continue
                    
                except Exception as e:
                    print(f"[ERRO] Erro ao enviar mensagem: {str(e)}")
                    log_to_file(f"[ERRO] Erro ao enviar mensagem para chamado {row['idtbchamados']}: {str(e)}")
                    print("[INFO] NÃO marcando como processado devido ao erro - tentará novamente na próxima verificação")
                    
                    # Registra tentativa falhada (opcional - você pode criar uma coluna attempts na tabela)
                    # cursor.execute("UPDATE queue_tbchamados_teste SET attempts = COALESCE(attempts, 0) + 1 WHERE id = %s", (row["id"],))
                    
                    # TEMPO CONFIGURÁVEL - FROTAS: Pausa em caso de erro (ajustável)
                    print(f"[INFO] Aguardando {TEMPO_ERRO_PAUSA} segundos após erro antes de continuar...")
                    time.sleep(TEMPO_ERRO_PAUSA)  # Tempo configurável - pausa em caso de erro
                    continue
            else:
                agora = datetime.now()
                proxima_verificacao = datetime.now().replace(second=0, microsecond=0)
                proxima_verificacao = proxima_verificacao.replace(minute=proxima_verificacao.minute + 5)
                print(f"[INFO] {agora.strftime('%d/%m/%Y %H:%M:%S')} - Verificando novas respostas... (nenhuma encontrada)")
                print(f"[INFO] Próxima verificação em {TEMPO_VERIFICACAO_FILA//60} minutos às {proxima_verificacao.strftime('%H:%M:%S')}")
        
        except Exception as e:
            agora = datetime.now()
            print(f"[ERRO] {agora.strftime('%d/%m/%Y %H:%M:%S')} - Erro na consulta: {e}")
            log_to_file(f"[ERRO] Erro na consulta do banco: {e}")
            # Reconecta em caso de erro de conexão
            try:
                conn.ping(reconnect=True)
            except:
                conn = mysql.connector.connect(**config)
                cursor = conn.cursor(dictionary=True)
        
        # TEMPO CONFIGURÁVEL - FROTAS: Frequência de consulta ao banco (ajustável)
        print(f"[INFO] Nenhum chamado na fila. Aguardando {TEMPO_VERIFICACAO_FILA} segundos para próxima verificação...")
        time.sleep(TEMPO_VERIFICACAO_FILA)  # Tempo configurável - intervalo de verificação da fila

if __name__ == "__main__":
    try:
        main()
    except KeyboardInterrupt:
        print("\nWorker encerrado.")
