#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Conversor de SQL dump para CSV
Extrai colunas da definição CREATE TABLE e depois processa os INSERT statements
"""

import csv
import re
import sys
from pathlib import Path

SQL_PATH = r"C:\Users\User-0062\Desktop\030326\tbfuncionario\tbfuncionario_backup030327.sql"
OUT_CSV  = r"C:\Users\User-0062\Desktop\030326\tbfuncionario\tbfuncionario_backup030327.csv"

TABLE_DUMP = "tbfuncionario_backup030327"
DELIMITER = ","
SHOW_PROGRESS = True


def detect_encoding(p: Path) -> str:
    """Detecta encoding do arquivo testando múltiplos codecs"""
    encodings = ["utf-8", "latin1", "cp1252", "iso-8859-1"]
    
    for enc in encodings:
        try:
            with p.open("r", encoding=enc) as f:
                f.read(1000)
            print(f"✓ Encoding detectado: {enc}")
            return enc
        except (UnicodeDecodeError, LookupError):
            continue
    
    print("⚠️  Nenhum encoding padrão funcionou, usando latin1")
    return "latin1"


def extract_columns_from_create_table(sql_content: str, table_name: str) -> list:
    """
    Extrai os nomes das colunas a partir da definição CREATE TABLE
    Retorna lista com nomes das colunas na ordem correta
    """
    # Procura por CREATE TABLE `nome_da_tabela` (
    pattern = rf"CREATE\s+TABLE\s+`{re.escape(table_name)}`\s*\((.*?)\)\s*ENGINE"
    match = re.search(pattern, sql_content, re.DOTALL | re.IGNORECASE)
    
    if not match:
        print(f"❌ Não encontrou CREATE TABLE para {table_name}")
        return []
    
    create_block = match.group(1)
    columns = []
    
    # Extrai cada linha que define uma coluna (começa com backtick)
    for line in create_block.split('\n'):
        line = line.strip()
        if not line or line.startswith('KEY') or line.startswith('PRIMARY') or line.startswith('UNIQUE') or line.startswith('INDEX'):
            continue
        
        # Procura por `coluna_name` no início da linha
        col_match = re.match(r"`([^`]+)`", line)
        if col_match:
            col_name = col_match.group(1)
            columns.append(col_name)
    
    print(f"✓ Colunas extraídas: {len(columns)}")
    if columns:
        print(f"  Primeiras: {columns[:5]}")
        print(f"  Últimas: {columns[-3:]}")
    
    return columns


def split_rows(values_block: str):
    """
    Recebe: (..),(..),(..)
    Retorna lista de strings, cada uma com o conteúdo interno sem parênteses.
    Respeita aspas e escapes.
    """
    rows = []
    i = 0
    n = len(values_block)
    in_str = False
    escape = False
    depth = 0
    start = None

    while i < n:
        ch = values_block[i]

        if in_str:
            if escape:
                escape = False
            elif ch == "\\":
                escape = True
            elif ch == "'":
                in_str = False
        else:
            if ch == "'":
                in_str = True
            elif ch == "(":
                if depth == 0:
                    start = i + 1
                depth += 1
            elif ch == ")":
                depth -= 1
                if depth == 0 and start is not None:
                    rows.append(values_block[start:i])
                    start = None

        i += 1

    return rows


def split_fields(row_text: str):
    """Separa por vírgula respeitando strings em aspas simples e escapes."""
    fields = []
    buf = []
    in_str = False
    escape = False

    i = 0
    n = len(row_text)

    while i < n:
        ch = row_text[i]

        if in_str:
            buf.append(ch)
            if escape:
                escape = False
            elif ch == "\\":
                escape = True
            elif ch == "'":
                in_str = False
        else:
            if ch == "'":
                in_str = True
                buf.append(ch)
            elif ch == ",":
                fields.append("".join(buf).strip())
                buf = []
            else:
                buf.append(ch)

        i += 1

    fields.append("".join(buf).strip())
    return fields


def decode_sql_value(token: str) -> str:
    """Decodifica um valor SQL (string, número, NULL, etc)"""
    token = token.strip()
    if token.upper() == "NULL":
        return ""

    # string: '...'
    if len(token) >= 2 and token[0] == "'" and token[-1] == "'":
        s = token[1:-1]
        # Ordem importa: \\ antes de \'
        s = s.replace("\\\\", "\x00")  # marcador temporário
        s = s.replace("\\'", "'")
        s = s.replace("\x00", "\\")
        s = s.replace("\\n", "\n").replace("\\r", "\r").replace("\\t", "\t")
        s = s.replace("\\0", "\0")
        return s

    return token


def iter_insert_statements(content: str, table: str):
    """
    Processa INSERT statements na string de conteúdo.
    Retorna cada INSERT completo.
    """
    insert_pattern = rf"INSERT\s+INTO\s+`{re.escape(table)}`\s+VALUES\s*(.+?);"
    
    for match in re.finditer(insert_pattern, content, re.DOTALL | re.IGNORECASE):
        values_block = match.group(1).strip()
        yield values_block


def extract_values_block(values_block: str) -> str:
    """Retorna o bloco VALUES já extraído"""
    return values_block.strip()


def main():
    p = Path(SQL_PATH)
    if not p.exists():
        print(f"❌ Arquivo não encontrado: {SQL_PATH}")
        return False

    print(f"📂 Lendo arquivo SQL: {SQL_PATH}")
    enc = detect_encoding(p)
    
    # Lê o conteúdo completo
    try:
        with p.open("r", encoding=enc, errors="replace") as f:
            sql_content = f.read()
    except Exception as e:
        print(f"❌ Erro ao ler arquivo: {e}")
        return False

    print(f"\n📋 Extraindo definição de colunas...")
    columns = extract_columns_from_create_table(sql_content, TABLE_DUMP)
    
    if not columns:
        print("❌ Não foi possível extrair as colunas da tabela")
        return False

    print(f"\n✍️  Processando dados...")
    outp = Path(OUT_CSV)
    outp.parent.mkdir(parents=True, exist_ok=True)

    total = 0
    errors = 0
    
    try:
        with outp.open("w", newline="", encoding="utf-8") as out:
            w = csv.writer(out, delimiter=DELIMITER, quoting=csv.QUOTE_MINIMAL)
            w.writerow(columns)
            print(f"✍️  Cabeçalho: {len(columns)} colunas escritas")

            for values_block in iter_insert_statements(sql_content, TABLE_DUMP):
                if not values_block:
                    continue

                try:
                    for row in split_rows(values_block):
                        if not row.strip():
                            continue
                        
                        try:
                            raw_fields = split_fields(row)
                            decoded = [decode_sql_value(t) for t in raw_fields]

                            # Valida número de campos
                            if len(decoded) != len(columns):
                                print(f"⚠️  Registro #{total+1}: {len(decoded)} campos (esperado {len(columns)})")
                                
                                # Ajusta preenchendo ou truncando
                                if len(decoded) < len(columns):
                                    decoded.extend([""] * (len(columns) - len(decoded)))
                                else:
                                    decoded = decoded[:len(columns)]
                                errors += 1

                            w.writerow(decoded)
                            total += 1
                            
                            # Mostra progresso
                            if SHOW_PROGRESS and total % 10000 == 0:
                                print(f"  ✓ {total} registros processados...")
                                
                        except Exception as e:
                            print(f"❌ Erro ao processar registro #{total+1}: {str(e)[:80]}")
                            errors += 1
                            continue
                
                except Exception as e:
                    print(f"❌ Erro ao processar bloco VALUES: {str(e)[:80]}")
                    continue

        print(f"\n{'='*70}")
        print(f"✅ CSV gerado com SUCESSO!")
        print(f"📁 Arquivo: {OUT_CSV}")
        print(f"📊 Total de registros: {total}")
        print(f"⚠️  Erros/Ajustes: {errors}")
        print(f"🔤 Colunas: {len(columns)}")
        print(f"{'='*70}")
        return True
        
    except Exception as e:
        print(f"❌ Erro fatal ao escrever CSV: {e}")
        import traceback
        traceback.print_exc()
        return False


if __name__ == "__main__":
    try:
        success = main()
        sys.exit(0 if success else 1)
    except Exception as e:
        print(f"❌ Erro fatal: {e}")
        import traceback
        traceback.print_exc()
        sys.exit(1)
