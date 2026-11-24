
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
import time
import threading

# Importar bibliotecas para automação de mouse
try:
    import pyautogui
    from pynput import mouse, keyboard
except Exception:
    pyautogui = None
    mouse = None
    keyboard = None

if pyautogui:
    pyautogui.PAUSE = 0
    pyautogui.MINIMUM_DURATION = 0
    pyautogui.MINIMUM_SLEEP = 0

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
        self.maximize_window()
        
        self.conn = None
        self.cursor = None
        self.current_record = None
        self.order_var = tk.StringVar(value="ASC")
        self.recorded_sequences = {1: [], 2: []}
        self.recording_sequence_id = None
        self.current_replay_sequence = None
        self.replay_mode = None  # "manual" | "automatic"
        self.manual_replay_sequence = 1
        self.is_saving_sequence = False
        self.is_recording_mouse = False
        self.is_mouse_record_paused = False
        self.is_replaying_mouse = False
        self.record_start_time = None
        self.pause_started_at = None
        self.total_pause_seconds = 0.0
        self.mouse_listener = None
        self.stop_replay_event = threading.Event()
        self.replay_keyboard_listener = None
        self.empty_queue_retry_after_id = None
        self.hibernate_duration_seconds = 60
        self.hibernate_started_at = None
        self.hibernate_remaining_seconds = 0
        self.hibernate_timer_after_id = None
        self.default_replay_speed_multiplier = 2
        self.empty_queue_replay_speed_multiplier = 0.001
        self.replay_speed_multiplier = self.default_replay_speed_multiplier
        self.replay_slowed_for_empty_queue = False
        self.disable_empty_queue_clicks = True
        self.observacao_collapsed_height = 2
        self.observacao_expanded_height = 6
        self.response_collapsed_height = 3
        self.response_expanded_height = 8
        self.observacao_expanded = False
        self.response_expanded = False
        
        # Cores e estilo
        self.bg_color = "#f0f0f0"
        self.primary_color = "#0066cc"
        self.success_color = "#28a745"
        self.warning_color = "#ffc107"
        self.danger_color = "#dc3545"
        self.frota_color = "#ff6600"  # Laranja para diferenciar de chamados
        
        self.root.configure(bg=self.bg_color)
        
        self.setup_ui()
        self._update_manual_replay_button_label()
        self.connect_database()
        self.carregar_passos_banco(1, silent=True)
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

        ttk.Label(info_frame, text="Observação:", font=("Arial", 9, "bold")).grid(row=5, column=0, sticky=tk.NW, pady=2)
        self.observacao_text = scrolledtext.ScrolledText(
            info_frame,
            height=self.observacao_collapsed_height,
            width=50,
            font=("Arial", 9),
            wrap=tk.WORD,
            state=tk.DISABLED
        )
        self.observacao_text.grid(row=5, column=1, sticky=(tk.W, tk.E), padx=(10, 0), pady=2)
        self.observacao_text.bind("<Button-1>", self.toggle_observacao_text)
        
        # Campo Telefone
        phone_frame = ttk.LabelFrame(main_frame, text="📞 Número de Telefone", padding="10")
        phone_frame.grid(row=3, column=0, columnspan=2, sticky=(tk.W, tk.E), pady=(0, 10))
        phone_frame.columnconfigure(0, weight=1)
        phone_frame.columnconfigure(1, weight=1)
        
        self.phone_text = tk.Text(phone_frame, height=2, width=40, font=("Arial", 12, "bold"), wrap=tk.WORD)
        self.phone_text.grid(row=0, column=0, columnspan=2, sticky=(tk.W, tk.E), pady=(0, 8))
        self.phone_text.config(state=tk.DISABLED)

        self.copy_query_btn = tk.Button(
            phone_frame,
            text="📋 Copiar Consulta",
            command=self.copy_query,
            bg=self.frota_color,
            fg="white",
            font=("Arial", 10, "bold"),
            cursor="hand2",
            relief=tk.RAISED,
            bd=2
        )
        self.copy_query_btn.grid(row=1, column=0, padx=(0, 4), sticky=(tk.W, tk.E))

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
        self.copy_phone_btn.grid(row=1, column=1, padx=(4, 0), sticky=(tk.W, tk.E))

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
        self.response_text.bind("<Button-1>", self.toggle_response_text)
        
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
        
        self.hibernate_timer_label = tk.Label(
            main_frame,
            text="⏳ Hibernação: ativa somente com fila vazia",
            font=("Arial", 10, "bold"),
            bg=self.bg_color,
            fg="#856404",
            anchor=tk.W
        )
        self.hibernate_timer_label.grid(row=6, column=0, columnspan=2, sticky=(tk.W, tk.E), pady=(0, 8))

        # Frame de confirmação
        confirm_frame = ttk.LabelFrame(main_frame, text="⚡ Ações", padding="15")
        confirm_frame.grid(row=7, column=0, columnspan=2, sticky=(tk.W, tk.E), pady=(0, 10))
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

        # Frame para automação de mouse
        automation_frame = ttk.LabelFrame(main_frame, text="🖱️ Automação Manual de Mouse", padding="10")
        automation_frame.grid(row=8, column=0, columnspan=2, sticky=(tk.W, tk.E), pady=(0, 10))
        for i in range(5):
            automation_frame.columnconfigure(i, weight=1)

        self.record_mouse_btn = tk.Button(
            automation_frame,
            text="⏺️ Gravar Mov",
            command=lambda: self.toggle_mouse_recording(1),
            bg=self.primary_color,
            fg="white",
            font=("Arial", 8, "bold"),
            cursor="hand2",
            relief=tk.RAISED,
            bd=2,
            height=2
        )
        self.record_mouse_btn.grid(row=0, column=0, padx=4, sticky=(tk.W, tk.E))

        self.pause_mouse_btn = tk.Button(
            automation_frame,
            text="⏸️ Pausar Grav",
            command=self.toggle_pause_mouse_recording,
            bg=self.warning_color,
            fg="black",
            font=("Arial", 8, "bold"),
            cursor="hand2",
            relief=tk.RAISED,
            bd=2,
            height=2,
            state=tk.DISABLED
        )
        self.pause_mouse_btn.grid(row=0, column=1, padx=4, sticky=(tk.W, tk.E))

        self.record_mouse_step2_btn = tk.Button(
            automation_frame,
            text="⏺️ Gravar Movimento 2",
            command=lambda: self.toggle_mouse_recording(2),
            bg="#5a2ca0",
            fg="white",
            font=("Arial", 8, "bold"),
            cursor="hand2",
            relief=tk.RAISED,
            bd=2,
            height=2
        )
        self.record_mouse_step2_btn.grid(row=0, column=2, padx=4, sticky=(tk.W, tk.E))
        self.record_mouse_step2_btn.grid_remove()

        self.replay_mouse_btn = tk.Button(
            automation_frame,
            text="🔁 Reproduzir Mov",
            command=self.toggle_mouse_replay_manual,
            bg=self.success_color,
            fg="white",
            font=("Arial", 8, "bold"),
            cursor="hand2",
            relief=tk.RAISED,
            bd=2,
            height=2
        )
        self.replay_mouse_btn.grid(row=0, column=2, padx=4, sticky=(tk.W, tk.E))

        self.save_steps_btn = tk.Button(
            automation_frame,
            text="💾 Salvar Mov",
            command=lambda: self.salvar_passos_banco(1),
            bg="#17a2b8",
            fg="white",
            font=("Arial", 8, "bold"),
            cursor="hand2",
            relief=tk.RAISED,
            bd=2,
            height=2
        )
        self.save_steps_btn.grid(row=0, column=3, padx=4, sticky=(tk.W, tk.E))

        self.save_steps_2_btn = tk.Button(
            automation_frame,
            text="💾 Salvar Passo 2",
            command=lambda: self.salvar_passos_banco(2),
            bg="#138496",
            fg="white",
            font=("Arial", 8, "bold"),
            cursor="hand2",
            relief=tk.RAISED,
            bd=2,
            height=2
        )
        self.save_steps_2_btn.grid(row=1, column=4, padx=4, sticky=(tk.W, tk.E))
        self.save_steps_2_btn.grid_remove()

        self.automation_hint_label = ttk.Label(
            automation_frame,
            text="Teclas: X para | ↓ -25% velo | ↑ +25% velo",
            font=("Arial", 9)
        )
        self.automation_hint_label.grid(row=1, column=0, columnspan=4, sticky=tk.W, pady=(8, 0))
        
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
        self.status_label.grid(row=9, column=0, columnspan=2, sticky=(tk.W, tk.E), pady=(10, 0))

        self.pending_label = tk.Label(
            main_frame,
            text="Pendentes (processed_at IS NULL): -",
            font=("Arial", 9, "bold"),
            bg=self.bg_color,
            fg=self.frota_color,
            anchor=tk.W,
            padx=2
        )
        self.pending_label.grid(row=10, column=0, columnspan=2, sticky=(tk.W, tk.E), pady=(8, 0))

        # Atalhos para copiar consultas SQL
        query_shortcuts_frame = ttk.LabelFrame(main_frame, text="📄 Consultas SQL (atalhos)", padding="10")
        query_shortcuts_frame.grid(row=11, column=0, columnspan=2, sticky=(tk.W, tk.E), pady=(10, 0))
        query_shortcuts_frame.columnconfigure(0, weight=1)
        query_shortcuts_frame.columnconfigure(1, weight=1)
        query_shortcuts_frame.columnconfigure(2, weight=1)

        self.copy_query_1_btn = tk.Button(
            query_shortcuts_frame,
            text="📋 Copiar Consulta 1",
            command=lambda: self.copy_query("SELECT * FROM bdfrota.queue_ocorrencia_resposta;", "Consulta 1"),
            bg=self.frota_color,
            fg="white",
            font=("Arial", 10, "bold"),
            cursor="hand2",
            relief=tk.RAISED,
            bd=2
        )
        self.copy_query_1_btn.grid(row=0, column=0, padx=(0, 4), sticky=(tk.W, tk.E))

        self.copy_query_2_btn = tk.Button(
            query_shortcuts_frame,
            text="📋 Copiar Consulta 2",
            command=lambda: self.copy_query("SELECT * FROM bdfrota.tbchamados;", "Consulta 2"),
            bg=self.frota_color,
            fg="white",
            font=("Arial", 10, "bold"),
            cursor="hand2",
            relief=tk.RAISED,
            bd=2
        )
        self.copy_query_2_btn.grid(row=0, column=1, padx=4, sticky=(tk.W, tk.E))

        self.copy_query_3_btn = tk.Button(
            query_shortcuts_frame,
            text="📋 Copiar Consulta 3",
            command=lambda: self.copy_query("SELECT * FROM bdfrota.tbchamados WHERE data_fech IS NOT NULL;", "Consulta 3"),
            bg=self.frota_color,
            fg="white",
            font=("Arial", 10, "bold"),
            cursor="hand2",
            relief=tk.RAISED,
            bd=2
        )
        self.copy_query_3_btn.grid(row=0, column=2, padx=(4, 0), sticky=(tk.W, tk.E))

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

    def _update_manual_replay_button_label(self):
        self.replay_mouse_btn.config(text=f"🔁 Reproduzir Mov ({self.manual_replay_sequence})")

    def _format_mm_ss(self, total_seconds):
        total_seconds = max(0, int(total_seconds))
        minutos = total_seconds // 60
        segundos = total_seconds % 60
        return f"{minutos}:{segundos:02d}"

    def _stop_hibernation_timer(self):
        if self.hibernate_timer_after_id:
            self.root.after_cancel(self.hibernate_timer_after_id)
            self.hibernate_timer_after_id = None
        self.hibernate_started_at = None
        self.hibernate_remaining_seconds = 0
        self.hibernate_timer_label.config(text="⏳ Hibernação: ativa somente com fila vazia")

    def _start_hibernation_timer(self):
        if self.hibernate_timer_after_id:
            self.root.after_cancel(self.hibernate_timer_after_id)
            self.hibernate_timer_after_id = None

        self.hibernate_started_at = time.time()
        self.hibernate_remaining_seconds = self.hibernate_duration_seconds

        def _tick():
            if not self.hibernate_started_at:
                return
            elapsed = int(time.time() - self.hibernate_started_at)
            self.hibernate_remaining_seconds = max(self.hibernate_duration_seconds - elapsed, 0)

            if self.hibernate_remaining_seconds > 0:
                self.hibernate_timer_label.config(
                    text=f"⏳ Hibernação: {self._format_mm_ss(self.hibernate_remaining_seconds)}"
                )
                self.hibernate_timer_after_id = self.root.after(1000, _tick)
                return

            self.hibernate_timer_label.config(text="⏳ Hibernação: 0:00 | verificando fila...")
            self.hibernate_timer_after_id = None
            self.root.after(150, self.load_next_record)

        _tick()

    def _record_elapsed_time(self):
        if not self.record_start_time:
            return 0
        elapsed = time.time() - self.record_start_time - self.total_pause_seconds
        return max(elapsed, 0)

    def toggle_mouse_recording(self, sequence_id=1):
        if not mouse:
            messagebox.showwarning("Dependências", "Instale: pip install pynput pyautogui")
            return
        if self.is_replaying_mouse:
            messagebox.showwarning("Aviso", "Pare a reprodução antes de gravar.")
            return

        if not self.is_recording_mouse:
            self.recording_sequence_id = sequence_id
            self.recorded_sequences[sequence_id] = []
            self.is_recording_mouse = True
            self.is_mouse_record_paused = False
            self.record_start_time = time.time()
            self.pause_started_at = None
            self.total_pause_seconds = 0.0
            self.manual_replay_sequence = sequence_id
            self._update_manual_replay_button_label()

            self.mouse_listener = mouse.Listener(
                on_move=self._on_mouse_move_record,
                on_click=self._on_mouse_click_record
            )
            self.mouse_listener.start()

            if sequence_id == 1:
                self.record_mouse_btn.config(text="⏹️ Parar Gravação 1", bg=self.danger_color)
                self.record_mouse_step2_btn.config(state=tk.DISABLED)
            else:
                self.record_mouse_step2_btn.config(text="⏹️ Parar Gravação 2", bg=self.danger_color)
                self.record_mouse_btn.config(state=tk.DISABLED)
            self.pause_mouse_btn.config(state=tk.NORMAL)
            self.update_status(f"⏺️ Gravação do Movimento {sequence_id} iniciada", "info")
            return

        if self.recording_sequence_id and self.recording_sequence_id != sequence_id:
            messagebox.showwarning("Aviso", f"A gravação atual é do Movimento {self.recording_sequence_id}.")
            return
        self._stop_mouse_recording()

    def _stop_mouse_recording(self):
        seq = self.recording_sequence_id
        self.is_recording_mouse = False
        self.is_mouse_record_paused = False
        self.recording_sequence_id = None
        if self.mouse_listener:
            try:
                self.mouse_listener.stop()
            except Exception:
                pass
            self.mouse_listener = None
        self.record_mouse_btn.config(text="⏺️ Gravar Mov", bg=self.primary_color, state=tk.NORMAL)
        self.record_mouse_step2_btn.config(text="⏺️ Gravar Movimento 2", bg="#5a2ca0", state=tk.NORMAL)
        self.pause_mouse_btn.config(state=tk.DISABLED, text="⏸️ Pausar Grav")
        total = len(self.recorded_sequences.get(seq or 1, []))
        self.update_status(f"⏹️ Gravação parada (Movimento {seq}) | Eventos: {total}", "success")

    def toggle_pause_mouse_recording(self):
        if not self.is_recording_mouse:
            return
        if not self.is_mouse_record_paused:
            self.is_mouse_record_paused = True
            self.pause_started_at = time.time()
            self.pause_mouse_btn.config(text="▶️ Retomar Gravação")
        else:
            if self.pause_started_at:
                self.total_pause_seconds += time.time() - self.pause_started_at
            self.pause_started_at = None
            self.is_mouse_record_paused = False
            self.pause_mouse_btn.config(text="⏸️ Pausar Grav")

    def _on_mouse_move_record(self, x, y):
        if not self.is_recording_mouse or self.is_mouse_record_paused:
            return
        self.recorded_sequences[self.recording_sequence_id].append({
            "tipo_acao": "movimento",
            "tempo_decorrido_ms": int(self._record_elapsed_time() * 1000),
            "posicao_x": x,
            "posicao_y": y,
        })

    def _on_mouse_click_record(self, x, y, button, pressed):
        if not self.is_recording_mouse or self.is_mouse_record_paused or not pressed:
            return
        if button not in (mouse.Button.left, mouse.Button.right):
            return
        self.recorded_sequences[self.recording_sequence_id].append({
            "tipo_acao": "clique",
            "tempo_decorrido_ms": int(self._record_elapsed_time() * 1000),
            "posicao_x": x,
            "posicao_y": y,
            "botao_mouse": "esquerda" if button == mouse.Button.left else "direita",
        })

    def toggle_mouse_replay_manual(self):
        self._toggle_mouse_replay("manual")

    def toggle_mouse_replay_auto(self):
        self._toggle_mouse_replay("automatic")

    def _toggle_mouse_replay(self, mode):
        if not pyautogui or not keyboard:
            messagebox.showwarning("Dependências", "Instale: pip install pynput pyautogui")
            return
        if self.is_recording_mouse:
            self._stop_mouse_recording()
        if self.is_replaying_mouse:
            self.stop_mouse_replay("⏹️ Reprodução interrompida")
            return

        self.replay_mode = mode
        self.is_replaying_mouse = True
        self.stop_replay_event.clear()
        self.record_mouse_btn.config(state=tk.DISABLED)
        self.record_mouse_step2_btn.config(state=tk.DISABLED)
        self.pause_mouse_btn.config(state=tk.DISABLED)
        self.replay_mouse_btn.config(text="⏹️ Parar Reprodução", bg=self.danger_color)
        threading.Thread(target=self._run_mouse_replay_loop, daemon=True).start()

    def _run_mouse_replay_loop(self):
        def on_press(key):
            try:
                ch = getattr(key, "char", "").lower()
                if ch == "x":
                    self.stop_replay_event.set()
                    return False
                elif key == keyboard.Key.down:
                    self.replay_speed_multiplier = max(0.25, self.replay_speed_multiplier * 0.75)
                    self.root.after(0, lambda m=self.replay_speed_multiplier: self.update_status(f"🐢 Velocidade diminuída → multiplicador: {m:.2f}x", "warning"))
                elif key == keyboard.Key.up:
                    self.replay_speed_multiplier = min(64, self.replay_speed_multiplier * 1.25)
                    self.root.after(0, lambda m=self.replay_speed_multiplier: self.update_status(f"🚀 Velocidade aumentada → multiplicador: {m:.2f}x (muito)", "info"))
            except Exception:
                return

        self.replay_keyboard_listener = keyboard.Listener(on_press=on_press)
        self.replay_keyboard_listener.start()
        try:
            while not self.stop_replay_event.is_set():
                if self.replay_mode == "automatic":
                    if self.has_unprocessed_records():
                        self.replay_speed_multiplier = self.default_replay_speed_multiplier
                        self.replay_slowed_for_empty_queue = False
                    else:
                        self.replay_speed_multiplier = self.empty_queue_replay_speed_multiplier
                        self.replay_slowed_for_empty_queue = True
                        self._idle_mouse_patrol()
                        continue
                sequence_id = self.manual_replay_sequence if self.replay_mode == "manual" else 1
                passos = self.recorded_sequences.get(sequence_id, []) or self.carregar_passos_banco(sequence_id, silent=True)
                if not passos:
                    time.sleep(1)
                    continue
                self._executar_passos(passos)
        finally:
            self.root.after(0, lambda: self.stop_mouse_replay("🛑 Reprodução finalizada"))

    def stop_mouse_replay(self, status_message=None):
        self.stop_replay_event.set()
        self.is_replaying_mouse = False
        if self.replay_keyboard_listener:
            try:
                self.replay_keyboard_listener.stop()
            except Exception:
                pass
            self.replay_keyboard_listener = None
        self._update_manual_replay_button_label()
        self.replay_mouse_btn.config(bg=self.success_color)
        self.record_mouse_btn.config(state=tk.NORMAL)
        self.record_mouse_step2_btn.config(state=tk.NORMAL)
        self.pause_mouse_btn.config(state=tk.NORMAL if self.is_recording_mouse else tk.DISABLED)
        self.replay_mode = None
        if status_message:
            self.update_status(status_message, "warning")
    
    def connect_database(self):
        """Conecta ao banco de dados"""
        try:
            self.conn = mysql.connector.connect(**config)
            self.cursor = self.conn.cursor(dictionary=True)
            self.update_status("✅ Conectado ao banco de dados (bdfrota)", "success")
        except Exception as e:
            messagebox.showerror("Erro de Conexão", f"Não foi possível conectar ao banco de dados:\n{e}")
            self.update_status(f"❌ Erro de conexão: {e}", "error")

    def ensure_database_connection(self):
        """Garante conexão ativa com MySQL."""
        try:
            if not self.conn or not self.conn.is_connected():
                self.conn = mysql.connector.connect(**config)
                self.cursor = self.conn.cursor(dictionary=True)
                self.update_status("🔌 Reconectado ao MySQL", "warning")
                return True
            self.conn.ping(reconnect=True, attempts=3, delay=2)
            if not self.cursor:
                self.cursor = self.conn.cursor(dictionary=True)
            return True
        except Exception as e:
            self.cursor = None
            self.update_status(f"❌ Falha ao reconectar MySQL: {e}", "error")
            return False

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
        if not self.ensure_database_connection():
            return
        self.cursor.execute(
            "UPDATE queue_ocorrencia_resposta SET processed_at = NOW() WHERE id = %s",
            (record_id,)
        )
        self.conn.commit()
        self.update_status(f"ℹ️ Registro {record_id} ignorado: {motivo}", "warning")

    def get_pending_count(self):
        """Retorna a quantidade de registros sem processed_at."""
        if not self.ensure_database_connection():
            return 0

        self.cursor.execute(
            "SELECT COUNT(*) AS total FROM queue_ocorrencia_resposta WHERE processed_at IS NULL"
        )
        resultado = self.cursor.fetchone() or {}
        return int(resultado.get("total") or 0)
    
    def load_next_record(self):
        """Carrega o próximo registro não processado"""
        try:
            if self.empty_queue_retry_after_id:
                self.root.after_cancel(self.empty_queue_retry_after_id)
                self.empty_queue_retry_after_id = None

            if not self.ensure_database_connection():
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
                self._stop_hibernation_timer()
                self.current_record = row
                self.display_record(row)
                self.update_status(
                    f"📋 Registro carregado - ID: {row['id']} | Chamado Frota: {row['idtbchamados']} | Faltam: {pendentes}",
                    "info"
                )
            else:
                self.current_record = None
                self.clear_fields()
                self._start_hibernation_timer()
                self.update_status(
                    f"✅ Nenhum registro pendente encontrado! | Faltam: {pendentes}. Hibernando 1 minuto.",
                    "success"
                )
                
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
        self.observacao_expanded = False
        self.observacao_text.config(state=tk.NORMAL, height=self.observacao_collapsed_height)
        self.observacao_text.delete(1.0, tk.END)
        self.observacao_text.insert(1.0, str(record.get('observacao') or "-"))
        self.observacao_text.config(state=tk.DISABLED)
        
        # Formata telefone
        numero_formatado = record.get('_telefone_formatado') or self.normalize_brazilian_mobile(record.get('telefone')) or "NÚMERO INVÁLIDO"
        
        # Atualiza campo de telefone
        self.phone_text.config(state=tk.NORMAL)
        self.phone_text.delete(1.0, tk.END)
        self.phone_text.insert(1.0, numero_formatado)
        self.phone_text.config(state=tk.DISABLED)
        
        # Exibe resposta original
        self.response_expanded = False
        self.response_text.config(state=tk.NORMAL, height=self.response_collapsed_height)
        self.response_text.delete(1.0, tk.END)
        self.response_text.insert(1.0, record['resposta'])
        self.response_text.config(state=tk.DISABLED)
        
        # Monta mensagem completa com verificação de fechamento
        data_fech = record.get('data_fech')
        if data_fech:
            # Chamado está fechado - mensagem alternativa
            data_formatada = data_fech.strftime("%d/%m/%Y %H:%M:%S") if hasattr(data_fech, 'strftime') else str(data_fech)
            mensagem = (
                f"Notificação de atualização em chamado na Frota!\n"
                f"Chamado fechado: {data_formatada}\n"
                f"Última resposta no sistema Frota referente ao chamado nº {record['idtbchamados']}:\n\n"
                f"  {record['resposta']}\n\n"
                f"Para mais detalhes, acesse seus chamados pelo link:\n"
                f"https://ffa.painel-telecom.com:4242/frotas/chamados/listagemchamadoscliente.php\n\n"
                f"Esta é uma mensagem automática de resposta ou fechamento de chamado— não é possível respondê-la e as atualizações não ocorrem em tempo real. "
                f"Em caso de dúvidas, entre em contato com sua equipe na Frota ou verifique o chamado. Em caso de não ter aberto chamado, desconsidere esta notificação."
            )
        else:
            # Chamado aberto - mensagem padrão
            mensagem = (
                f"Notificação de atualização em chamado na Frota!\n"
                f"Houve uma nova resposta no sistema Frota referente ao chamado nº {record['idtbchamados']}:\n\n"
                f"  {record['resposta']}\n\n"
                f"Para mais detalhes, acesse seus chamados pelo link:\n"
                f"https://ffa.painel-telecom.com:4242/frotas/chamados/listagemchamadoscliente.php\n\n"
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
        self.observacao_expanded = False
        self.observacao_text.config(state=tk.NORMAL, height=self.observacao_collapsed_height)
        self.observacao_text.delete(1.0, tk.END)
        self.observacao_text.insert(1.0, "-")
        self.observacao_text.config(state=tk.DISABLED)
        
        self.phone_text.config(state=tk.NORMAL)
        self.phone_text.delete(1.0, tk.END)
        self.phone_text.config(state=tk.DISABLED)
        
        self.response_expanded = False
        self.response_text.config(state=tk.NORMAL, height=self.response_collapsed_height)
        self.response_text.delete(1.0, tk.END)
        self.response_text.config(state=tk.DISABLED)
        
        self.message_text.config(state=tk.NORMAL)
        self.message_text.delete(1.0, tk.END)
        self.message_text.config(state=tk.DISABLED)

    def _toggle_text_height(self, widget, attr_name, collapsed_height, expanded_height):
        expanded = getattr(self, attr_name)
        widget.config(height=collapsed_height if expanded else expanded_height)
        setattr(self, attr_name, not expanded)

    def toggle_observacao_text(self, _event=None):
        """Alterna o tamanho do campo Observação ao clicar."""
        self._toggle_text_height(
            self.observacao_text,
            "observacao_expanded",
            self.observacao_collapsed_height,
            self.observacao_expanded_height,
        )
        return "break"

    def toggle_response_text(self, _event=None):
        """Alterna o tamanho do campo Resposta Original ao clicar."""
        self._toggle_text_height(
            self.response_text,
            "response_expanded",
            self.response_collapsed_height,
            self.response_expanded_height,
        )
        return "break"
    
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

    def copy_query(self, query=None, label="Consulta"):
        """Copia uma consulta SQL para a área de transferência."""
        try:
            sql_query = query or "SELECT * FROM bdfrota.queue_ocorrencia_resposta;"
            self.root.clipboard_clear()
            self.root.clipboard_append(sql_query)
            self.root.update()
            self.update_status(f"📋 {label} copiada para área de transferência", "success")
            messagebox.showinfo("Copiado!", f"{label} copiada:\n{sql_query}")
        except Exception as e:
            messagebox.showerror("Erro", f"Erro ao copiar consulta:\n{e}")

    def maximize_window(self):
        """Abre a janela ocupando a metade esquerda da tela ao iniciar."""
        def _apply():
            sw = self.root.winfo_screenwidth()
            sh = self.root.winfo_screenheight()
            self.root.geometry(f"{sw // 2}x{sh}+0+0")
        self.root.after(1, _apply)
    
    def mark_as_processed(self):
        """Marca o registro como processado (usuário confirmou que enviou)"""
        if not self.current_record:
            messagebox.showwarning("Aviso", "Nenhum registro carregado.")
            return
        
        try:
            if not self.ensure_database_connection():
                messagebox.showerror("Erro", "Sem conexão com banco de dados.")
                return
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
            if not self.ensure_database_connection():
                messagebox.showerror("Erro", "Sem conexão com banco de dados.")
                return
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

    def salvar_passos_banco(self, sequence_id):
        """Salva passos 1/2 sem travar a UI."""
        if self.is_saving_sequence:
            messagebox.showwarning("Automação", "Já existe um salvamento em andamento.")
            return
        passos = list(self.recorded_sequences.get(sequence_id, []))
        if not passos:
            messagebox.showwarning("Aviso", f"Nenhum evento gravado para o passo {sequence_id}.")
            return

        self.manual_replay_sequence = sequence_id
        self._update_manual_replay_button_label()
        self.is_saving_sequence = True
        self.save_steps_btn.config(state=tk.DISABLED)
        self.save_steps_2_btn.config(state=tk.DISABLED)
        self.update_status(f"💾 Salvando Passo {sequence_id} em segundo plano...", "info")
        threading.Thread(
            target=self._salvar_passos_banco_worker,
            args=(sequence_id, passos),
            daemon=True,
        ).start()

    def _salvar_passos_banco_worker(self, sequence_id, passos):
        try:
            conexao = mysql.connector.connect(**config)
            cursor = conexao.cursor()
            cursor.execute("DELETE FROM tb_mouse_steps WHERE registro_id = %s", (sequence_id,))
            valores = [
                (
                    sequence_id,
                    passo_numero,
                    passo["tipo_acao"],
                    passo.get("posicao_x"),
                    passo.get("posicao_y"),
                    passo.get("botao_mouse"),
                    passo["tempo_decorrido_ms"],
                )
                for passo_numero, passo in enumerate(passos, 1)
            ]
            cursor.executemany(
                """
                INSERT INTO tb_mouse_steps
                (registro_id, passo_numero, tipo_acao, posicao_x, posicao_y, botao_mouse, tempo_decorrido_ms)
                VALUES (%s, %s, %s, %s, %s, %s, %s)
                """,
                valores,
            )
            conexao.commit()
            cursor.close()
            conexao.close()
            self.root.after(0, lambda: self._finalizar_salvamento(sequence_id, len(passos), None))
        except Exception as e:
            self.root.after(0, lambda err=e: self._finalizar_salvamento(sequence_id, 0, err))

    def _finalizar_salvamento(self, sequence_id, total_eventos, erro):
        self.is_saving_sequence = False
        self.save_steps_btn.config(state=tk.NORMAL)
        self.save_steps_2_btn.config(state=tk.NORMAL)
        if erro:
            messagebox.showerror("Erro", f"Erro ao salvar passo {sequence_id}:\n{erro}")
            self.update_status(f"❌ Erro ao salvar passo {sequence_id}", "error")
            return
        self.update_status(f"✅ Passo {sequence_id} salvo com {total_eventos} eventos", "success")

    def carregar_passos_banco(self, sequence_id, silent=False):
        try:
            conexao = mysql.connector.connect(**config)
            cursor = conexao.cursor(dictionary=True)
            cursor.execute(
                """
                SELECT tipo_acao, posicao_x, posicao_y, botao_mouse, tempo_decorrido_ms
                FROM tb_mouse_steps
                WHERE registro_id = %s
                ORDER BY passo_numero ASC
                """,
                (sequence_id,),
            )
            passos = cursor.fetchall()
            cursor.close()
            conexao.close()
            self.recorded_sequences[sequence_id] = passos or []
            if passos and not silent:
                self.update_status(f"📂 Passo {sequence_id} carregado do banco", "info")
            return passos or []
        except Exception as e:
            if not silent:
                self.update_status(f"❌ Erro ao carregar passo {sequence_id}: {e}", "error")
            return []

    def _executar_passos(self, passos):
        """Executa uma vez os passos com replay acelerado e sem pausas implícitas do pyautogui."""
        inicio = time.perf_counter()
        clicks_desativados = self.disable_empty_queue_clicks and self.replay_slowed_for_empty_queue
        for passo in passos:
            if self.stop_replay_event.is_set():
                break
            esperado_ms = int((passo.get("tempo_decorrido_ms") or 0) / max(self.replay_speed_multiplier, 1))
            decorrido_ms = int((time.perf_counter() - inicio) * 1000)
            espera_ms = esperado_ms - decorrido_ms
            if espera_ms > 1:
                time.sleep(espera_ms / 1000)
            x = passo.get("posicao_x")
            y = passo.get("posicao_y")
            if passo.get("tipo_acao") == "movimento":
                pyautogui.moveTo(x, y, duration=0, _pause=False)
            elif passo.get("tipo_acao") == "clique":
                if clicks_desativados:
                    continue
                botao = "right" if "direita" in str(passo.get("botao_mouse", "")).lower() else "left"
                pyautogui.click(x, y, button=botao, _pause=False)

    def _idle_mouse_patrol(self):
        """Com fila vazia no automático, move apenas para cima/baixo sem cliques."""
        try:
            x, y = pyautogui.position()
            deslocamento = 35
            espera = 0.35
            pyautogui.moveTo(x, max(0, y - deslocamento), duration=0, _pause=False)
            time.sleep(espera)
            pyautogui.moveTo(x, y + deslocamento, duration=0, _pause=False)
            time.sleep(espera)
        except Exception:
            time.sleep(0.5)

    def has_unprocessed_records(self):
        try:
            conexao = mysql.connector.connect(**config)
            cursor = conexao.cursor()
            cursor.execute(
                """
                SELECT 1
                FROM queue_ocorrencia_resposta
                WHERE processed_at IS NULL
                LIMIT 1
                """
            )
            existe = cursor.fetchone() is not None
            cursor.close()
            conexao.close()
            return existe
        except Exception:
            return False
    
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
            if self.empty_queue_retry_after_id:
                self.root.after_cancel(self.empty_queue_retry_after_id)
                self.empty_queue_retry_after_id = None
            self._stop_hibernation_timer()
            self.stop_mouse_replay()
            self._stop_mouse_recording()
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