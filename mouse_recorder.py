# -*- coding: utf-8 -*-
"""
mouse_recorder_v2.py
Script para gravar e reproduzir movimentos do mouse e teclas pressionadas.
Necessita: pip install pynput pyautogui
"""

import time
import pyautogui
from pynput import mouse, keyboard

movements = []
keys = []

print("Iniciando gravação de movimentos do mouse e teclas...")
print("Pressione o botão direito do mouse para parar a gravação.")
print("A gravação começará em 3 segundos...")
time.sleep(3)

print("Gravação iniciada!")
start_time = time.time()

# Funções para gravar movimentos do mouse

def on_move(x, y):
    timestamp = time.time() - start_time
    movements.append((timestamp, x, y))
    print(f"Moveu para ({x}, {y}) em {timestamp:.2f}s")

def on_click(x, y, button, pressed):
    if button == mouse.Button.right and pressed:
        print("Gravação finalizada.")
        return False

# Funções para gravar teclas

def on_press(key):
    timestamp = time.time() - start_time
    try:
        k = key.char
    except AttributeError:
        # Para teclas especiais, pega o nome legível
        k = key.name if hasattr(key, 'name') else str(key)
    keys.append((timestamp, k))
    print(f"Tecla pressionada: {k} em {timestamp:.2f}s")

# Inicia listeners
mouse_listener = mouse.Listener(on_move=on_move, on_click=on_click)
keyboard_listener = keyboard.Listener(on_press=on_press)

mouse_listener.start()
keyboard_listener.start()

mouse_listener.join()
keyboard_listener.stop()

print(f"Movimentos gravados: {len(movements)}")
print(f"Teclas gravadas: {len(keys)}")

input("Pressione ENTER para reproduzir os movimentos e teclas...")

print("Reproduzindo movimentos...")

# Reproduz movimentos do mouse
for i, (timestamp, x, y) in enumerate(movements):
    if i == 0:
        time.sleep(timestamp)
    else:
        time.sleep(timestamp - movements[i-1][0])
    pyautogui.moveTo(x, y)
    print(f"Movido para ({x}, {y})")

print("Reproduzindo teclas...")
for i, (timestamp, k) in enumerate(keys):
    if i == 0:
        time.sleep(timestamp)
    else:
        time.sleep(timestamp - keys[i-1][0])
    # Trata teclas especiais
    if k.lower() == 'enter':
        pyautogui.press('enter')
    elif k.lower() == 'ctrl':
        pyautogui.keyDown('ctrl')
    elif k.lower() == 'ctrl_l' or k.lower() == 'ctrl_r':
        pyautogui.keyDown('ctrl')
    elif k.lower() == 'c':
        pyautogui.press('c')
    elif k.lower() == 'v':
        pyautogui.press('v')
    elif k.lower() == 'esc':
        pyautogui.press('esc')
    elif k.lower() == 'shift':
        pyautogui.keyDown('shift')
    elif k.lower() == 'shift_l' or k.lower() == 'shift_r':
        pyautogui.keyDown('shift')
    else:
        pyautogui.typewrite(k)
    print(f"Tecla reproduzida: {k}")

print("Reprodução finalizada.")
