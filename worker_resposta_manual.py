# -*- coding: utf-8 -*-
"""
Worker Manual GUI - Processamento Manual de Respostas de Frotas
Exibe interface gráfica para revisar e processar mensagens manualmente
"""

import os
import sys
import tkinter as tk
from tkinter import ttk, messagebox, scrolledtext
from datetime import datetime
import mysql.connector
import re
import random
import time

# Importar biblioteca para movimento de mouse
try:
    import pyautogui
    PYAUTOGUI_AVAILABLE = True
except ImportError:
    PYAUTOGUI_AVAILABLE = False

# Config do MySQL 
config = {
    "user": "blvp",
    "password": "@F3r7n6c2",
    "host": "10.17.0.5",
    "database": "bdfrota",
    "autocommit": True,
}

class WorkerManualGUI:
    def __init__(self, root):
        self.root = root
        self.root.title("Worker Manual - Processamento de Respostas (FROTAS)")
        self.root.geometry("700x650")
        self.root.resizable(True, True)
        
        self.conn = None
        self.cursor = None
        self.current_record = None
        self.order_var = tk.StringVar(value="ASC")
        
        # Cores e estilo
        self.bg_color = "#f0f0f0"
        self.primary_color = "#0066cc"
        self.success_color = "#28a745"
        self.warning_color = "#ffc107"
        self.danger_color = "#dc3545"
        self.frota_color = "#ff6600"  # Laranja para diferenciar de chamados
        
        self.root.configure(bg=self.bg_color)
        
        self.setup_ui()
        self.connect_database()
        self.load_next_record()
    
    def setup_ui(self):
        """Configura a interface gráfica"""
        
        # Frame com barra lateral de rolagem
        container = ttk.Frame(self.root)
        container.grid(row=0, column=0, sticky=(tk.W, tk.E, tk.N, tk.S))

        self.canvas = tk.Canvas(container, bg=self.bg_color, highlightthickness=0)
        scrollbar = ttk.Scrollbar(container, orient="vertical", command=self.canvas.yview)
        self.canvas.configure(yscrollcommand=scrollbar.set)

        scrollbar.pack(side=tk.RIGHT, fill=tk.Y)
        self.canvas.pack(side=tk.LEFT, fill=tk.BOTH, expand=True)

        main_frame = ttk.Frame(self.canvas, padding="20")
        self.canvas_window = self.canvas.create_window((0, 0), window=main_frame, anchor="nw")

        main_frame.bind("<Configure>", lambda e: self.canvas.configure(scrollregion=self.canvas.bbox("all")))
        self.canvas.bind("<Configure>", lambda e: self.canvas.itemconfigure(self.canvas_window, width=e.width))
        self.canvas.bind_all("<MouseWheel>", self._on_mousewheel)
        self.canvas.bind_all("<Button-4>", self._on_mousewheel)
        self.canvas.bind_all("<Button-5>", self._on_mousewheel)

        self.root.columnconfigure(0, weight=1)
        self.root.rowconfigure(0, weight=1)
        main_frame.columnconfigure(0, weight=1)
        
        # Título
        title_label = tk.Label(
            main_frame, 
            text="🚗 Processamento Manual - Sistema Frotas",
            font=("Arial", 16, "bold"),
            bg=self.bg_color,
            fg=self.frota_color
        )
        title_label.grid(row=0, column=0, columnspan=2, pady=(0, 20))

        # Seleção de ordem dos IDs
        order_frame = ttk.LabelFrame(main_frame, text="🔃 Ordem de Processamento", padding="10")
        order_frame.grid(row=1, column=0, columnspan=2, sticky=(tk.W, tk.E), pady=(0, 10))

        oldest_radio = ttk.Radiobutton(
            order_frame,
            text="IDs mais antigos (ASC)",
            variable=self.order_var,
            value="ASC",
            command=self.change_processing_order
        )
        oldest_radio.grid(row=0, column=0, sticky=tk.W, padx=(0, 20))

        newest_radio = ttk.Radiobutton(
            order_frame,
            text="IDs mais recentes (DESC)",
            variable=self.order_var,
            value="DESC",
            command=self.change_processing_order
        )
        newest_radio.grid(row=0, column=1, sticky=tk.W)
        
        # Info do chamado
        info_frame = ttk.LabelFrame(main_frame, text="Informações do Chamado", padding="10")
        info_frame.grid(row=2, column=0, columnspan=2, sticky=(tk.W, tk.E), pady=(0, 10))
        info_frame.columnconfigure(1, weight=1)
        
        ttk.Label(info_frame, text="ID do Registro:", font=("Arial", 9, "bold")).grid(row=0, column=0, sticky=tk.W, pady=2)
        self.id_label = ttk.Label(info_frame, text="-", font=("Arial", 9))
        self.id_label.grid(row=0, column=1, sticky=tk.W, padx=(10, 0), pady=2)
        
        ttk.Label(info_frame, text="Chamado Nº:", font=("Arial", 9, "bold")).grid(row=1, column=0, sticky=tk.W, pady=2)
        self.chamado_label = ttk.Label(info_frame, text="-", font=("Arial", 9))
        self.chamado_label.grid(row=1, column=1, sticky=tk.W, padx=(10, 0), pady=2)

        ttk.Label(info_frame, text="Criado em:", font=("Arial", 9, "bold")).grid(row=2, column=0, sticky=tk.W, pady=2)
        self.created_at_label = ttk.Label(info_frame, text="-", font=("Arial", 9))
        self.created_at_label.grid(row=2, column=1, sticky=tk.W, padx=(10, 0), pady=2)

        ttk.Label(info_frame, text="Data Fech.:", font=("Arial", 9, "bold")).grid(row=3, column=0, sticky=tk.W, pady=2)
        self.data_fech_label = ttk.Label(info_frame, text="-", font=("Arial", 9))
        self.data_fech_label.grid(row=3, column=1, sticky=tk.W, padx=(10, 0), pady=2)

        ttk.Label(info_frame, text="Tipo Chamado:", font=("Arial", 9, "bold")).grid(row=4, column=0, sticky=tk.W, pady=2)
        self.tipodechamado_label = ttk.Label(info_frame, text="-", font=("Arial", 9))
        self.tipodechamado_label.grid(row=4, column=1, sticky=tk.W, padx=(10, 0), pady=2)

        ttk.Label(info_frame, text="Observação:", font=("Arial", 9, "bold")).grid(row=5, column=0, sticky=tk.W, pady=2)
        self.observacao_label = ttk.Label(info_frame, text="-", font=("Arial", 9), wraplength=460, justify=tk.LEFT)
        self.observacao_label.grid(row=5, column=1, sticky=tk.W, padx=(10, 0), pady=2)
        
        # Campo Telefone
        phone_frame = ttk.LabelFrame(main_frame, text="📞 Número de Telefone", padding="10")
        phone_frame.grid(row=3, column=0, columnspan=2, sticky=(tk.W, tk.E), pady=(0, 10))
        phone_frame.columnconfigure(0, weight=1)
        
        self.phone_text = tk.Text(phone_frame, height=2, width=40, font=("Arial", 12, "bold"), wrap=tk.WORD)
        self.phone_text.grid(row=0, column=0, sticky=(tk.W, tk.E), padx=(0, 5))
        self.phone_text.config(state=tk.DISABLED)
        
        self.copy_phone_btn = tk.Button(
            phone_frame, 
            text="📋 Copiar Telefone",
            command=self.copy_phone,
            bg=self.frota_color,
            fg="white",
            font=("Arial", 10, "bold"),
            cursor="hand2",
            relief=tk.RAISED,
            bd=2
        )
        self.copy_phone_btn.grid(row=0, column=1, padx=(5, 0))
        
        # Campo Resposta Original
        response_frame = ttk.LabelFrame(main_frame, text="📝 Resposta Original do Sistema", padding="10")
        response_frame.grid(row=4, column=0, columnspan=2, sticky=(tk.W, tk.E), pady=(0, 10))
        response_frame.columnconfigure(0, weight=1)
        
        self.response_text = scrolledtext.ScrolledText(
            response_frame, 
            height=3, 
            width=60, 
            font=("Arial", 9),
            wrap=tk.WORD,
            state=tk.DISABLED,
            bg="#ffffcc"
        )
        self.response_text.grid(row=0, column=0, sticky=(tk.W, tk.E), columnspan=2)

        self.copy_query_btn = tk.Button(
            response_frame,
            text="📋 Copiar Consulta",
            command=self.copy_query,
            bg=self.frota_color,
            fg="white",
            font=("Arial", 10, "bold"),
            cursor="hand2",
            relief=tk.RAISED,
            bd=2
        )
        self.copy_query_btn.grid(row=1, column=0, sticky=tk.W, pady=(8, 0))
        
        # Campo Mensagem
        message_frame = ttk.LabelFrame(main_frame, text="💬 Mensagem Completa para Enviar", padding="10")
        message_frame.grid(row=5, column=0, columnspan=2, sticky=(tk.W, tk.E, tk.N, tk.S), pady=(0, 10))
        message_frame.columnconfigure(0, weight=1)
        message_frame.rowconfigure(0, weight=1)
        main_frame.rowconfigure(5, weight=1)
        
        self.message_text = scrolledtext.ScrolledText(
            message_frame, 
            height=10, 
            width=60, 
            font=("Arial", 10),
            wrap=tk.WORD,
            state=tk.DISABLED
        )
        self.message_text.grid(row=0, column=0, sticky=(tk.W, tk.E, tk.N, tk.S), padx=(0, 5))
        
        self.copy_message_btn = tk.Button(
            message_frame, 
            text="📋 Copiar\nMensagem",
            command=self.copy_message,
            bg=self.frota_color,
            fg="white",
            font=("Arial", 10, "bold"),
            cursor="hand2",
            relief=tk.RAISED,
            bd=2,
            height=3
        )
        self.copy_message_btn.grid(row=0, column=1, padx=(5, 0), sticky=(tk.N, tk.S))
        
        # Frame de confirmação
        confirm_frame = ttk.LabelFrame(main_frame, text="⚡ Ações", padding="15")
        confirm_frame.grid(row=6, column=0, columnspan=2, sticky=(tk.W, tk.E), pady=(0, 10))
        confirm_frame.columnconfigure(0, weight=1)
        confirm_frame.columnconfigure(1, weight=1)
        confirm_frame.columnconfigure(2, weight=1)
        
        question_label = tk.Label(
            confirm_frame, 
            text="Você enviou esta mensagem?",
            font=("Arial", 11, "bold"),
            bg=self.bg_color
        )
        question_label.grid(row=0, column=0, columnspan=3, pady=(0, 10))
        
        self.yes_btn = tk.Button(
            confirm_frame,
            text="✅ SIM\n(Marcar como Processado)",
            command=self.mark_as_processed,
            bg=self.success_color,
            fg="white",
            font=("Arial", 11, "bold"),
            cursor="hand2",
            relief=tk.RAISED,
            bd=3,
            height=3,
            width=20
        )
        self.yes_btn.grid(row=1, column=0, padx=5, sticky=(tk.W, tk.E))
        
        self.skip_btn = tk.Button(
            confirm_frame,
            text="⏭️ PULAR\n(Não Processar Agora)",
            command=self.skip_record,
            bg=self.warning_color,
            fg="black",
            font=("Arial", 11, "bold"),
            cursor="hand2",
            relief=tk.RAISED,
            bd=3,
            height=3,
            width=20
        )
        self.skip_btn.grid(row=1, column=1, padx=5, sticky=(tk.W, tk.E))
        
        self.no_btn = tk.Button(
            confirm_frame,
            text="❌ NÃO\n(Descartar Mensagem)",
            command=self.mark_as_discarded,
            bg=self.danger_color,
            fg="white",
            font=("Arial", 11, "bold"),
            cursor="hand2",
            relief=tk.RAISED,
            bd=3,
            height=3,
            width=20
        )
        self.no_btn.grid(row=1, column=2, padx=5, sticky=(tk.W, tk.E))
        
        # Status bar
        self.status_label = tk.Label(
            main_frame, 
            text="Aguardando...",
            font=("Arial", 9),
            bg="#e0e0e0",
            fg="#333",
            relief=tk.SUNKEN,
            anchor=tk.W,
            padx=10,
            pady=5
        )
        self.status_label.grid(row=7, column=0, columnspan=2, sticky=(tk.W, tk.E), pady=(10, 0))

        self.pending_label = tk.Label(
            main_frame,
            text="Pendentes (processed_at IS NULL): -",
            font=("Arial", 9, "bold"),
            bg=self.bg_color,
            fg=self.frota_color,
            anchor=tk.W,
            padx=2
        )
        self.pending_label.grid(row=8, column=0, columnspan=2, sticky=(tk.W, tk.E), pady=(8, 0))

    def change_processing_order(self):
        """Atualiza a ordem de processamento e recarrega a fila."""
        order = self.order_var.get()
        descricao = "mais antigos" if order == "ASC" else "mais recentes"
        self.update_status(f"🔃 Ordem alterada para IDs {descricao}", "info")
        self.load_next_record()

    def _on_mousewheel(self, event):
        """Permite rolagem da interface pela roda do mouse."""
        if event.num == 4:
            self.canvas.yview_scroll(-1, "units")
        elif event.num == 5:
            self.canvas.yview_scroll(1, "units")
        elif event.delta:
            self.canvas.yview_scroll(int(-1 * (event.delta / 120)), "units")
    
    def connect_database(self):
        """Conecta ao banco de dados"""
        try:
            self.conn = mysql.connector.connect(**config)
            self.cursor = self.conn.cursor(dictionary=True)
            self.update_status("✅ Conectado ao banco de dados (bdfrota)", "success")
        except Exception as e:
            messagebox.showerror("Erro de Conexão", f"Não foi possível conectar ao banco de dados:\n{e}")
            self.update_status(f"❌ Erro de conexão: {e}", "error")

    def normalize_brazilian_mobile(self, telefone):
        """Normaliza e valida número de celular brasileiro. Retorna +55XXXXXXXXXXX ou None."""
        digits = re.sub(r"\D", "", telefone or "")
        if not digits:
            return None

        if digits.startswith("00"):
            digits = digits[2:]

        if digits.startswith("55") and len(digits) == 13:
            national = digits[2:]
        elif len(digits) == 11:
            national = digits
        else:
            return None

        ddd = national[:2]
        numero = national[2:]

        if len(numero) != 9 or not numero.startswith("9"):
            return None

        ddds_validos = {
            "11", "12", "13", "14", "15", "16", "17", "18", "19",
            "21", "22", "24", "27", "28",
            "31", "32", "33", "34", "35", "37", "38",
            "41", "42", "43", "44", "45", "46", "47", "48", "49",
            "51", "53", "54", "55",
            "61", "62", "63", "64", "65", "66", "67", "68", "69",
            "71", "73", "74", "75", "77", "79",
            "81", "82", "83", "84", "85", "86", "87", "88", "89",
            "91", "92", "93", "94", "95", "96", "97", "98", "99",
        }
        if ddd not in ddds_validos:
            return None

        return f"+55{national}"

    def mark_record_processed(self, record_id, motivo):
        """Marca registro como processado sem interação de confirmação."""
        self.cursor.execute(
            "UPDATE queue_ocorrencia_resposta SET processed_at = NOW() WHERE id = %s",
            (record_id,)
        )
        self.conn.commit()
        self.update_status(f"ℹ️ Registro {record_id} ignorado: {motivo}", "warning")

    def get_pending_count(self):
        """Retorna a quantidade de registros sem processed_at."""
        if not self.cursor:
            return 0

        self.cursor.execute(
            "SELECT COUNT(*) AS total FROM queue_ocorrencia_resposta WHERE processed_at IS NULL"
        )
        resultado = self.cursor.fetchone() or {}
        return int(resultado.get("total") or 0)
    
    def load_next_record(self):
        """Carrega o próximo registro não processado"""
        try:
            if not self.cursor:
                self.update_status("❌ Sem conexão com banco de dados", "error")
                return
            
            row = None
            order = "DESC" if self.order_var.get() == "DESC" else "ASC"
            while True:
                query = f"""
                    SELECT 
                        qor.id,
                        qor.idtbchamados,
                        qor.resposta,
                        qor.telefone,
                        qor.created_at,
                        tc.data_fech,
                        tc.tipodechamado,
                        tc.observacao
                    FROM queue_ocorrencia_resposta qor
                    LEFT JOIN tbchamados tc ON tc.idtbchamados = qor.idtbchamados
                    WHERE qor.processed_at IS NULL 
                    AND qor.telefone IS NOT NULL 
                    AND qor.telefone != ''
                    ORDER BY qor.id {order}
                    LIMIT 1
                """
                self.cursor.execute(query)

                candidato = self.cursor.fetchone()
                if not candidato:
                    break

                resposta = (candidato.get('resposta') or '').strip()
                if not resposta:
                    self.mark_record_processed(candidato['id'], "resposta vazia/nula")
                    continue

                numero_formatado = self.normalize_brazilian_mobile(candidato.get('telefone'))
                if not numero_formatado:
                    self.mark_record_processed(candidato['id'], "telefone inválido ou não celular")
                    continue

                candidato['_telefone_formatado'] = numero_formatado
                row = candidato
                break
            
            pendentes = self.get_pending_count()
            self.pending_label.config(text=f"Pendentes (processed_at IS NULL): {pendentes}")

            if row:
                self.current_record = row
                self.display_record(row)
                self.update_status(
                    f"📋 Registro carregado - ID: {row['id']} | Chamado Frota: {row['idtbchamados']} | Faltam: {pendentes}",
                    "info"
                )
            else:
                self.current_record = None
                self.clear_fields()
                self.update_status(
                    f"✅ Nenhum registro pendente encontrado! | Faltam: {pendentes}",
                    "success"
                )
                messagebox.showinfo("Fila Vazia", "Não há mais registros pendentes para processar no sistema Frotas.")
                
        except Exception as e:
            messagebox.showerror("Erro", f"Erro ao carregar registro:\n{e}")
            self.update_status(f"❌ Erro ao carregar: {e}", "error")
    
    def display_record(self, record):
        """Exibe os dados do registro na interface"""
        # Atualiza labels de info
        self.id_label.config(text=str(record['id']))
        self.chamado_label.config(text=str(record['idtbchamados']))
        created_at = record.get('created_at')
        created_text = created_at.strftime("%d/%m/%Y %H:%M:%S") if created_at else "-"
        self.created_at_label.config(text=created_text)

        data_fech = record.get('data_fech')
        data_fech_text = data_fech.strftime("%d/%m/%Y %H:%M:%S") if data_fech else "-"
        self.data_fech_label.config(text=data_fech_text)
        self.tipodechamado_label.config(text=str(record.get('tipodechamado') or "-"))
        self.observacao_label.config(text=str(record.get('observacao') or "-"))
        
        # Formata telefone
        numero_formatado = record.get('_telefone_formatado') or self.normalize_brazilian_mobile(record.get('telefone')) or "NÚMERO INVÁLIDO"
        
        # Atualiza campo de telefone
        self.phone_text.config(state=tk.NORMAL)
        self.phone_text.delete(1.0, tk.END)
        self.phone_text.insert(1.0, numero_formatado)
        self.phone_text.config(state=tk.DISABLED)
        
        # Exibe resposta original
        self.response_text.config(state=tk.NORMAL)
        self.response_text.delete(1.0, tk.END)
        self.response_text.insert(1.0, record['resposta'])
        self.response_text.config(state=tk.DISABLED)
        
        # Monta mensagem completa
        mensagem = (
            f"Notificação de atualização em chamado na Frota!\n"
            f"Houve uma nova resposta no sistema Frota referente ao chamado nº {record['idtbchamados']}:\n\n"
            f"  {record['resposta']}\n\n"
            f"Para mais detalhes, acesse seus chamados pelo link:\n"
            f"https://ffasip.ddns.net:4242/frotas/chamados/listagemchamadoscliente.php\n\n"
            f"Esta é uma mensagem automática de resposta ou fechamento de chamado— não é possível respondê-la e as atualizações não ocorrem em tempo real. "
            f"Em caso de dúvidas, entre em contato com sua equipe na Frota ou verifique o chamado."
        )
        
        # Atualiza campo de mensagem
        self.message_text.config(state=tk.NORMAL)
        self.message_text.delete(1.0, tk.END)
        self.message_text.insert(1.0, mensagem)
        self.message_text.config(state=tk.DISABLED)
    
    def clear_fields(self):
        """Limpa todos os campos da interface"""
        self.id_label.config(text="-")
        self.chamado_label.config(text="-")
        self.created_at_label.config(text="-")
        self.data_fech_label.config(text="-")
        self.tipodechamado_label.config(text="-")
        self.observacao_label.config(text="-")
        
        self.phone_text.config(state=tk.NORMAL)
        self.phone_text.delete(1.0, tk.END)
        self.phone_text.config(state=tk.DISABLED)
        
        self.response_text.config(state=tk.NORMAL)
        self.response_text.delete(1.0, tk.END)
        self.response_text.config(state=tk.DISABLED)
        
        self.message_text.config(state=tk.NORMAL)
        self.message_text.delete(1.0, tk.END)
        self.message_text.config(state=tk.DISABLED)
    
    def copy_phone(self):
        """Copia o telefone para a área de transferência"""
        try:
            phone = self.phone_text.get(1.0, tk.END).strip()
            if phone:
                self.root.clipboard_clear()
                self.root.clipboard_append(phone)
                self.root.update()
                self.update_status(f"📋 Telefone copiado: {phone}", "success")
                messagebox.showinfo("Copiado!", f"Telefone copiado:\n{phone}")
        except Exception as e:
            messagebox.showerror("Erro", f"Erro ao copiar telefone:\n{e}")
    
    def copy_message(self):
        """Copia a mensagem para a área de transferência"""
        try:
            message = self.message_text.get(1.0, tk.END).strip()
            if message:
                self.root.clipboard_clear()
                self.root.clipboard_append(message)
                self.root.update()
                self.update_status("📋 Mensagem copiada para área de transferência", "success")
                messagebox.showinfo("Copiado!", "Mensagem copiada para a área de transferência!")
        except Exception as e:
            messagebox.showerror("Erro", f"Erro ao copiar mensagem:\n{e}")

    def copy_query(self):
        """Copia a consulta SQL da tabela principal para a área de transferência."""
        try:
            query = "SELECT * FROM bdfrota.queue_ocorrencia_resposta;"
            self.root.clipboard_clear()
            self.root.clipboard_append(query)
            self.root.update()
            self.update_status("📋 Consulta da tabela copiada para área de transferência", "success")
            messagebox.showinfo("Copiado!", f"Consulta copiada:\n{query}")
        except Exception as e:
            messagebox.showerror("Erro", f"Erro ao copiar consulta:\n{e}")
    
    def mark_as_processed(self):
        """Marca o registro como processado (usuário confirmou que enviou)"""
        if not self.current_record:
            messagebox.showwarning("Aviso", "Nenhum registro carregado.")
            return
        
        try:
            record_id = self.current_record['id']
            chamado_id = self.current_record['idtbchamados']
            
            self.cursor.execute(
                "UPDATE queue_ocorrencia_resposta SET processed_at = NOW() WHERE id = %s",
                (record_id,)
            )
            self.conn.commit()
            
            self.update_status(f"✅ Registro {record_id} (Chamado Frota {chamado_id}) marcado como processado", "success")
            messagebox.showinfo("Sucesso", f"Registro {record_id} marcado como processado!")
            
            # Log em arquivo
            self.log_action(f"Registro {record_id} do chamado {chamado_id} marcado como processado manualmente")
            
            # Carrega próximo registro
            self.load_next_record()
            
        except Exception as e:
            messagebox.showerror("Erro", f"Erro ao marcar como processado:\n{e}")
            self.update_status(f"❌ Erro ao processar: {e}", "error")
    
    def mark_as_discarded(self):
        """Marca o registro como processado/descartado (usuário não quer enviar)"""
        if not self.current_record:
            messagebox.showwarning("Aviso", "Nenhum registro carregado.")
            return
        
        confirm = messagebox.askyesno(
            "Confirmar Descarte",
            "Tem certeza que deseja DESCARTAR esta mensagem?\n\n"
            "Ela será marcada como processada e NÃO será enviada."
        )
        
        if not confirm:
            return
        
        try:
            record_id = self.current_record['id']
            chamado_id = self.current_record['idtbchamados']
            
            self.cursor.execute(
                "UPDATE queue_ocorrencia_resposta SET processed_at = NOW() WHERE id = %s",
                (record_id,)
            )
            self.conn.commit()
            
            self.update_status(f"🗑️ Registro {record_id} (Chamado Frota {chamado_id}) descartado", "warning")
            messagebox.showinfo("Descartado", f"Registro {record_id} foi descartado e marcado como processado.")
            
            # Log em arquivo
            self.log_action(f"Registro {record_id} do chamado {chamado_id} descartado manualmente")
            
            # Carrega próximo registro
            self.load_next_record()
            
        except Exception as e:
            messagebox.showerror("Erro", f"Erro ao descartar registro:\n{e}")
            self.update_status(f"❌ Erro ao descartar: {e}", "error")
    
    def skip_record(self):
        """Pula o registro atual sem processar (mantém na fila)"""
        if not self.current_record:
            messagebox.showwarning("Aviso", "Nenhum registro carregado.")
            return
        
        record_id = self.current_record['id']
        chamado_id = self.current_record['idtbchamados']
        
        self.update_status(f"⏭️ Registro {record_id} (Chamado Frota {chamado_id}) pulado", "warning")
        messagebox.showinfo("Pulado", f"Registro {record_id} foi pulado e permanece na fila.")
        
        # Log em arquivo
        self.log_action(f"Registro {record_id} do chamado {chamado_id} pulado")
        
        # Carrega próximo registro
        self.load_next_record()
    
    def update_status(self, message, status_type="info"):
        """Atualiza a barra de status"""
        colors = {
            "success": "#d4edda",
            "error": "#f8d7da",
            "warning": "#fff3cd",
            "info": "#d1ecf1"
        }
        
        text_colors = {
            "success": "#155724",
            "error": "#721c24",
            "warning": "#856404",
            "info": "#0c5460"
        }
        
        bg_color = colors.get(status_type, "#e0e0e0")
        fg_color = text_colors.get(status_type, "#333")
        
        timestamp = datetime.now().strftime("%H:%M:%S")
        self.status_label.config(text=f"[{timestamp}] {message}", bg=bg_color, fg=fg_color)
    
    def log_action(self, message):
        """Registra ação em arquivo de log"""
        try:
            log_dir = "logs"
            if not os.path.exists(log_dir):
                os.makedirs(log_dir)
            timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
            log_file = os.path.join(log_dir, "worker_frotas_manual.log")
            with open(log_file, "a", encoding="utf-8") as f:
                f.write(f"[{timestamp}] {message}\n")
        except Exception as e:
            print(f"[ERRO] Falha ao escrever no log: {e}")
    
    def on_closing(self):
        """Tratamento ao fechar a janela"""
        if messagebox.askokcancel("Sair", "Deseja fechar o Worker Manual de Frotas?"):
            if self.conn:
                try:
                    self.conn.close()
                except:
                    pass
            self.root.destroy()

def main():
    """Função principal"""
    root = tk.Tk()
    app = WorkerManualGUI(root)
    root.protocol("WM_DELETE_WINDOW", app.on_closing)
    root.mainloop()

if __name__ == "__main__":
    main()