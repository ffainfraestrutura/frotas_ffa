# -*- coding: utf-8 -*-
"""
Keep Awake Mouse Worker
Move o mouse continuamente para evitar bloqueio/espera da tela.
"""

import tkinter as tk
from datetime import datetime
import threading
import time

try:
    import pyautogui
except Exception:
    pyautogui = None


class KeepAwakeApp:
    def __init__(self, root):
        self.root = root
        self.root.title("Keep Awake - Mouse")
        self.root.geometry("500x240")
        self.root.resizable(False, False)

        self.running = False
        self.worker_thread = None
        self.stop_event = threading.Event()
        self.edge_padding_px = 2
        self.travel_duration_seconds = 20

        if pyautogui:
            pyautogui.PAUSE = 0
            pyautogui.MINIMUM_DURATION = 0
            pyautogui.MINIMUM_SLEEP = 0

        self._build_ui()
        self._bind_shortcuts()
        self.root.focus_force()

    def _build_ui(self):
        frame = tk.Frame(self.root, padx=16, pady=16)
        frame.pack(fill=tk.BOTH, expand=True)

        title = tk.Label(frame, text="Keep Awake (Mouse)", font=("Arial", 14, "bold"))
        title.pack(anchor="w")

        subtitle = tk.Label(
            frame,
            text="Move o mouse lentamente do topo até a base da tela, sem parar.",
            font=("Arial", 10),
            fg="#444"
        )
        subtitle.pack(anchor="w", pady=(4, 12))

        btn_row = tk.Frame(frame)
        btn_row.pack(fill=tk.X)

        self.start_btn = tk.Button(
            btn_row,
            text="Iniciar",
            width=14,
            bg="#198754",
            fg="white",
            font=("Arial", 10, "bold"),
            command=self.start
        )
        self.start_btn.pack(side=tk.LEFT, padx=(0, 8))

        self.pause_btn = tk.Button(
            btn_row,
            text="Pausar reprodução",
            width=18,
            bg="#dc3545",
            fg="white",
            font=("Arial", 10, "bold"),
            command=self.pause,
            state=tk.DISABLED
        )
        self.pause_btn.pack(side=tk.LEFT)

        shortcuts_bar = tk.Label(
            frame,
            text="ATALHOS: Q = INICIAR  |  W = PAUSAR",
            font=("Arial", 10, "bold"),
            fg="#0c5460"
        )
        shortcuts_bar.pack(anchor="w", pady=(10, 0))

        self.status = tk.Label(
            frame,
            text="Status: parado",
            font=("Arial", 10, "bold"),
            fg="#333"
        )
        self.status.pack(anchor="w", pady=(14, 4))

        self.last_run = tk.Label(frame, text="Último movimento: -", font=("Arial", 9), fg="#555")
        self.last_run.pack(anchor="w")

        shortcuts = tk.Label(frame, text="Dica: clique na janela antes de usar os atalhos.", font=("Arial", 9), fg="#666")
        shortcuts.pack(anchor="w", pady=(6, 0))

    def _bind_shortcuts(self):
        self.root.bind("<KeyPress-q>", self._start_from_key)
        self.root.bind("<KeyPress-Q>", self._start_from_key)
        self.root.bind("<KeyPress-w>", self._pause_from_key)
        self.root.bind("<KeyPress-W>", self._pause_from_key)
        self.root.bind_all("<KeyPress-q>", self._start_from_key)
        self.root.bind_all("<KeyPress-Q>", self._start_from_key)
        self.root.bind_all("<KeyPress-w>", self._pause_from_key)
        self.root.bind_all("<KeyPress-W>", self._pause_from_key)

    def _move_vertical_smooth(self, x, y_start, y_end, total_duration):
        steps = max(1, int(total_duration / 0.03))
        delta = (y_end - y_start) / steps
        y = float(y_start)

        for _ in range(steps):
            if self.stop_event.is_set():
                return False
            y += delta
            pyautogui.moveTo(x, int(y), duration=0, _pause=False)
            time.sleep(0.03)

        return True

    def _start_from_key(self, _event=None):
        self.start()

    def _pause_from_key(self, _event=None):
        self.pause()

    def _run_loop(self):
        if not pyautogui:
            self.root.after(0, lambda: self.status.config(text="Status: pyautogui não instalado", fg="#b02a37"))
            self.root.after(0, lambda: self.start_btn.config(state=tk.NORMAL))
            self.root.after(0, lambda: self.pause_btn.config(state=tk.DISABLED))
            self.running = False
            return

        try:
            width, height = pyautogui.size()
            x = int(width / 2)
            y_top = self.edge_padding_px
            y_bottom = max(y_top + 1, height - self.edge_padding_px)

            pyautogui.moveTo(x, y_top, duration=0.2, _pause=False)

            while not self.stop_event.is_set():
                if not self._move_vertical_smooth(x, y_top, y_bottom, self.travel_duration_seconds):
                    break

                now = datetime.now().strftime("%H:%M:%S")
                self.root.after(0, lambda t=now: self.last_run.config(text=f"Último movimento: {t}"))

                if not self._move_vertical_smooth(x, y_bottom, y_top, self.travel_duration_seconds):
                    break

        except Exception as exc:
            self.root.after(0, lambda e=exc: self.status.config(text=f"Status: erro ({e})", fg="#b02a37"))
            self.running = False
            self.root.after(0, lambda: self.start_btn.config(state=tk.NORMAL))
            self.root.after(0, lambda: self.pause_btn.config(state=tk.DISABLED))

    def start(self):
        if self.running:
            return

        self.running = True
        self.stop_event.clear()
        self.start_btn.config(state=tk.DISABLED)
        self.pause_btn.config(state=tk.NORMAL)
        self.status.config(text="Status: executando", fg="#146c43")
        self.worker_thread = threading.Thread(target=self._run_loop, daemon=True)
        self.worker_thread.start()

    def pause(self):
        self.running = False
        self.stop_event.set()

        if self.worker_thread and self.worker_thread.is_alive():
            self.worker_thread.join(timeout=0.3)
        self.worker_thread = None

        self.start_btn.config(state=tk.NORMAL)
        self.pause_btn.config(state=tk.DISABLED)
        self.status.config(text="Status: pausado", fg="#6c757d")


def main():
    root = tk.Tk()
    app = KeepAwakeApp(root)
    root.mainloop()


if __name__ == "__main__":
    main()
