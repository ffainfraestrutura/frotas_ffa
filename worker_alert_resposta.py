import time
import sys
import mysql.connector
from datetime import datetime
import os
import tkinter as tk
from tkinter import messagebox
import threading
import re


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
        log_file = os.path.join(log_dir, "worker_alert_resposta.log")
        with open(log_file, "a", encoding="utf-8") as f:
            f.write(f"[{timestamp}] {message}\n")
    except Exception as e:
        print(f"[ERRO] Falha ao escrever no log: {e}")

def verificar_data_anterior(resposta: str):
    """
    Verifica se há datas no formato dd/mm na resposta que sejam anteriores ao dia atual.
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
        datas_anteriores = []
        
        for dia_str, mes_str in matches:
            try:
                dia = int(dia_str)
                mes = int(mes_str)
                
                # Validar se dia e mês são válidos
                if not (1 <= dia <= 31 and 1 <= mes <= 12):
                    continue
                
                # Criar data com ano atual
                data_resposta = datetime(ano_atual, mes, dia)
                
                # Se a data for anterior ao dia atual, adicionar à lista
                if data_resposta.date() < hoje.date():
                    data_formatada = data_resposta.strftime("%d/%m/%Y")
                    datas_anteriores.append(data_formatada)
                    
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

def mostrar_alerta(titulo: str, mensagem: str):
    """Mostra um alerta usando tkinter"""
    def criar_alerta():
        # Criar janela principal (oculta)
        root = tk.Tk()
        root.withdraw()  # Oculta a janela principal
        
        # Mostrar messagebox
        messagebox.showwarning(titulo, mensagem)
        
        # Fechar aplicação
        root.quit()
        root.destroy()
    
    # Executar em thread separada para não bloquear o loop principal
    thread = threading.Thread(target=criar_alerta, daemon=True)
    thread.start()

def main():
    print("Worker Python Frota Alert — monitorando fila de respostas...\n")
    log_to_file("Worker Alert iniciado")

    conn = mysql.connector.connect(**config)
    cursor = conn.cursor(dictionary=True)
    
    # Controle para evitar spam de alertas
    ultimo_alerta_timestamp = None
    intervalo_minimo_alertas = 300  # 5 minutos em segundos
    
    while True:
        try:
            # Verificar horário permitido (8h às 19h)
            agora = datetime.now()
            hora_atual = agora.hour
            
            if hora_atual < 8 or hora_atual >= 19:
                print(f"[HIBERNAÇÃO] Fora do horário comercial ({hora_atual:02d}h) - Worker em hibernação")
                time.sleep(600)  # Aguarda 10 minutos
                continue
            
            # Buscar todos os registros não processados para aplicar as validações
            cursor.execute("""
                SELECT id, idtbchamados, resposta, telefone, created_at
                FROM queue_ocorrencia_resposta
                WHERE processed_at IS NULL 
                AND telefone IS NOT NULL 
                AND telefone != ''
                ORDER BY id ASC
            """)
            todos_registros = cursor.fetchall()
            
            # Aplicar as mesmas validações do worker principal
            registros_validos = []
            registros_invalidos = []
            
            for registro in todos_registros:
                telefone = registro.get('telefone')
                resposta = registro.get('resposta')
                
                # Validação 1: Telefone válido
                if not telefone or telefone.strip() == '':
                    registros_invalidos.append((registro['id'], f"Telefone vazio/nulo para chamado {registro['idtbchamados']}"))
                    continue
                
                # Validação 2: Resposta válida
                if not resposta or resposta.strip() == '':
                    registros_invalidos.append((registro['id'], f"Resposta vazia/nula para chamado {registro['idtbchamados']}"))
                    continue
                
                # Validação 3: Verificar datas anteriores na resposta
                tem_data_anterior, datas_encontradas, data_hoje = verificar_data_anterior(resposta)
                if tem_data_anterior:
                    datas_str = ", ".join(datas_encontradas)
                    registros_invalidos.append((registro['id'], f"Data anterior encontrada na resposta do chamado {registro['idtbchamados']} - Datas: {datas_str}"))
                    continue
                
                # Validação 4: Verificar duplicatas
                cursor.execute("""
                    SELECT COUNT(*) as total
                    FROM queue_ocorrencia_resposta
                    WHERE idtbchamados = %s 
                    AND resposta = %s 
                    AND id != %s
                    AND processed_at IS NOT NULL
                """, (registro['idtbchamados'], resposta, registro['id']))
                duplicata_result = cursor.fetchone()
                
                if duplicata_result and duplicata_result['total'] > 0:
                    registros_invalidos.append((registro['id'], f"Duplicata encontrada para chamado {registro['idtbchamados']} - mesmo chamado e resposta já processados"))
                    continue
                
                # Se passou por todas as validações, é válido
                registros_validos.append(registro)
            
            total_pendentes = len(registros_validos)
            total_invalidos = len(registros_invalidos)
            
            print(f"[INFO] {agora.strftime('%d/%m/%Y %H:%M:%S')} - Registros válidos pendentes: {total_pendentes}")
            if total_invalidos > 0:
                print(f"[INFO] Registros inválidos (ignorados): {total_invalidos}")
                for reg_id, motivo in registros_invalidos[:3]:  # Mostrar apenas os 3 primeiros
                    print(f"       - ID {reg_id}: {motivo}")
                if total_invalidos > 3:
                    print(f"       - ... e mais {total_invalidos - 3} inválidos")
            
            # Se há 5 ou mais registros VÁLIDOS pendentes, mostrar alerta
            if total_pendentes >= 5:
                # Verificar se já passou tempo suficiente desde o último alerta
                agora_timestamp = time.time()
                pode_alertar = (ultimo_alerta_timestamp is None or 
                              (agora_timestamp - ultimo_alerta_timestamp) >= intervalo_minimo_alertas)
                
                if pode_alertar:
                    # Usar os registros já validados (primeiros 5)
                    registros_pendentes = registros_validos[:5]
                    
                    # Criar mensagem detalhada
                    titulo = f"⚠️ ALERTA: {total_pendentes} Respostas Pendentes"
                    
                    mensagem = f"Há {total_pendentes} respostas pendentes na fila de processamento!\n\n"
                    mensagem += "Primeiros registros:\n"
                    mensagem += "=" * 50 + "\n"
                    
                    for i, reg in enumerate(registros_pendentes, 1):
                        created_at = reg['created_at'].strftime("%d/%m/%Y %H:%M:%S") if reg['created_at'] else "N/A"
                        resposta_resumo = safe(reg['resposta'])[:100] + "..." if len(safe(reg['resposta'])) > 100 else safe(reg['resposta'])
                        
                        mensagem += f"{i}. Chamado: {reg['idtbchamados']}\n"
                        mensagem += f"   Tel: {reg['telefone']}\n"
                        mensagem += f"   Data: {created_at}\n"
                        mensagem += f"   Resposta: {resposta_resumo}\n"
                        mensagem += "-" * 30 + "\n"
                    
                    if total_pendentes > 5:
                        mensagem += f"\n... e mais {total_pendentes - 5} registros pendentes.\n"
                    
                    mensagem += f"\nHorário do alerta: {agora.strftime('%d/%m/%Y %H:%M:%S')}"
                    mensagem += f"\nVerifique o sistema de processamento!"
                    
                    print(f"[ALERTA] Mostrando alerta para {total_pendentes} registros pendentes")
                    log_to_file(f"[ALERTA] {total_pendentes} registros pendentes - alerta exibido")
                    
                    # Mostrar alerta
                    mostrar_alerta(titulo, mensagem)
                    
                    # Atualizar timestamp do último alerta
                    ultimo_alerta_timestamp = agora_timestamp
                    
                    # Log detalhado dos registros pendentes
                    for reg in registros_pendentes:
                        log_to_file(f"[PENDENTE] ID:{reg['id']} Chamado:{reg['idtbchamados']} Tel:{reg['telefone']}")
                
                else:
                    tempo_restante = int(intervalo_minimo_alertas - (agora_timestamp - ultimo_alerta_timestamp))
                    print(f"[INFO] {total_pendentes} registros pendentes, mas alerta em cooldown ({tempo_restante}s restantes)")
            
            elif 1 <= total_pendentes <= 4:
                print(f"[INFO] {total_pendentes} registros válidos pendentes - dentro do normal (limite: 5)")
            
            else:
                if total_invalidos > 0:
                    print(f"[INFO] Fila de registros válidos vazia ✅ ({total_invalidos} inválidos ignorados)")
                else:
                    print(f"[INFO] Fila vazia - tudo processado ✅")
        
        except Exception as e:
            agora = datetime.now()
            print(f"[ERRO] {agora.strftime('%d/%m/%Y %H:%M:%S')} - Erro na consulta: {e}")
            log_to_file(f"[ERRO] Erro na consulta do banco: {e}")
            
            # Reconectar em caso de erro
            try:
                conn.ping(reconnect=True)
            except:
                conn = mysql.connector.connect(**config)
                cursor = conn.cursor(dictionary=True)
        
        # Verificar a cada 2 minutos
        time.sleep(300)

if __name__ == "__main__":
    try:
        main()
    except KeyboardInterrupt:
        print("\nWorker Alert encerrado.")
        log_to_file("Worker Alert encerrado pelo usuário")
