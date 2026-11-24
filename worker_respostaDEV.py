import time
import sys
import mysql.connector
import pywhatkit as kit
from datetime import datetime, timedelta
import os
import pyautogui
import io

# Config do MySQL
config = {
    "user": "blvp",
    "password": "@F3r7n6c2",
    "host": "10.17.0.5",
    "database": "bdfrota",
    "autocommit": True,
}

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

def is_working_hours():
    """Verifica se está dentro do horário de funcionamento (8h às 18h)"""
    now = datetime.now()
    current_hour = now.hour
    return 8 <= current_hour < 18

def wait_for_business_hours():
    """Aguarda até o horário de funcionamento iniciar (8h)"""
    while True:
        now = datetime.now()
        current_hour = now.hour
        
        if 8 <= current_hour < 18:
            break
        
        if current_hour < 8:
            next_start = now.replace(hour=8, minute=0, second=0, microsecond=0)
            wait_seconds = (next_start - now).total_seconds()
            print(f"[INFO] Aguardando início do horário de funcionamento às 08:00. Dormindo por {int(wait_seconds)} segundos...")
        else:  # current_hour >= 18
            next_start = now.replace(hour=8, minute=0, second=0, microsecond=0) + timedelta(days=1)
            wait_seconds = (next_start - now).total_seconds()
            print(f"[INFO] Fora do horário de funcionamento. Próximo início às 08:00. Dormindo por {int(wait_seconds)} segundos...")
        
        log_to_file(f"[INFO] Aguardando horário de funcionamento. Próximo início: {next_start.strftime('%d/%m/%Y %H:%M:%S')}")
        time.sleep(min(wait_seconds, 3600))  # Dorme no máximo 1 hora por vez

def main():
    print("Worker Python Frota Ativo — Sistema de Monitoramento 24h")
    print("=" * 60)
    print(f"[INFO] Horário de funcionamento: 08:00 às 18:00 (segunda a domingo)")
    print(f"[INFO] Horário atual: {datetime.now().strftime('%d/%m/%Y %H:%M:%S')}")
    print("=" * 60)
    
    # Loop principal que funciona 24h
    while True:
        try:
            # Aguarda até estar no horário de funcionamento
            wait_for_business_hours()
            
            print(f"\n[INÍCIO] Iniciando monitoramento às {datetime.now().strftime('%H:%M:%S')}")
            log_to_file("[INÍCIO] Worker iniciado - dentro do horário de funcionamento")
            
            # Debug: lista diretórios e arquivos na pasta atual
            print("[INFO] Diretórios e arquivos na pasta atual:")
            for item in os.listdir('.'):
                print(" -", item)

            conn = mysql.connector.connect(**config)
            cursor = conn.cursor(dictionary=True)

            # Loop de monitoramento (funciona enquanto estiver no horário)
            while is_working_hours():
                try:
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
                            f"Esta é uma mensagem automática — não é possível respondê-la e as atualizações não ocorrem em tempo real. "
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
                            # TEMPO CONFIGURÁVEL - FROTAS: +10 minutos para agendamento
                            minuto = (agora.minute + 10) % 60
                            
                            # Ajusta hora se minuto passou de 59 (voltou para 0)
                            if minuto < agora.minute:
                                hora = (hora + 1) % 24
                                
                            print(f"[INFO] Enviando para {numero_formatado} às {hora:02d}:{minuto:02d}")
                            
                            # Tenta enviar a mensagem (tempo maior para garantir carregamento completo)
                            kit.sendwhatmsg(numero_formatado, mensagem, hora, minuto, wait_time=40, tab_close=False)

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
                            

                            # TEMPO CONFIGURÁVEL - FROTAS: Verificação pós-envio
                            print("[INFO] Aguardando 30 segundos para garantir que o envio seja completado...")
                            time.sleep(30)  # 30 segundos - tempo maior para garantir envio completo
                            
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
                            
                            # TEMPO CONFIGURÁVEL - FROTAS: Intervalo entre envios
                            print("[INFO] Aguardando mais 60 segundos antes do próximo envio...")
                            time.sleep(60)  # 60 segundos = 1 minuto
                            
                        except Exception as e:
                            print(f"[ERRO] Erro ao enviar mensagem: {str(e)}")
                            log_to_file(f"[ERRO] Erro ao enviar mensagem para chamado {row['idtbchamados']}: {str(e)}")
                            print("[INFO] NÃO marcando como processado devido ao erro - tentará novamente na próxima verificação")
                            
                            # TEMPO CONFIGURÁVEL - FROTAS: Pausa em caso de erro
                            time.sleep(9)  # 9 segundos - pausa em caso de erro
                    else:
                        print("[INFO] Verificando novas respostas... (nenhuma encontrada)")
                
                except Exception as e:
                    print(f"[ERRO] Erro na consulta: {e}")
                    log_to_file(f"[ERRO] Erro na consulta do banco: {e}")
                    # Reconecta em caso de erro de conexão
                    try:
                        conn.ping(reconnect=True)
                    except:
                        conn = mysql.connector.connect(**config)
                        cursor = conn.cursor(dictionary=True)
                
                # TEMPO CONFIGURÁVEL - FROTAS: Frequência de consulta ao banco
                time.sleep(300)  # 5 minutos - intervalo de verificação
            
            # Saiu do horário de funcionamento
            print(f"\n[FIM] Horário de funcionamento encerrado às {datetime.now().strftime('%H:%M:%S')}")
            log_to_file("[FIM] Worker pausado - fora do horário de funcionamento")
            
            # Fecha conexão antes de aguardar
            try:
                cursor.close()
                conn.close()
            except:
                pass
                
        except KeyboardInterrupt:
            print("\n[INTERROMPIDO] Worker encerrado pelo usuário.")
            log_to_file("[INTERROMPIDO] Worker encerrado pelo usuário")
            break
        except Exception as e:
            print(f"[ERRO CRÍTICO] Erro no loop principal: {e}")
            log_to_file(f"[ERRO CRÍTICO] Erro no loop principal: {e}")
            print("[INFO] Aguardando 60 segundos antes de tentar novamente...")
            time.sleep(60)

if __name__ == "__main__":
    try:
        main()
    except KeyboardInterrupt:
        print("\nWorker encerrado.")
    except Exception as e:
        print(f"[ERRO FATAL] {e}")
        log_to_file(f"[ERRO FATAL] {e}")
