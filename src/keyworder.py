#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import pandas as pd
import spacy
from googletrans import Translator
import re

# ------------------------------
# CONFIGURAÇÕES
# ------------------------------
CSV_FILE = 'SB_publication_PMC.csv'  # Caminho para seu CSV
SQL_FILE = 'import_publicacoes.sql'  # Arquivo SQL de saída
TABLE_NAME = 'publicacoes'           # Nome da tabela no SQL

# ------------------------------
# FUNÇÕES AUXILIARES
# ------------------------------

def limpar_texto(texto):
    """Remove caracteres especiais e deixa só letras e espaços"""
    texto = re.sub(r'[^a-zA-Z0-9áéíóúãõç\s]', '', texto)
    texto = re.sub(r'\s+', ' ', texto)
    return texto.strip().lower()

def gerar_palavras_chave(nlp, texto):
    """Gera palavras-chave do texto usando spaCy (substantivos e nomes próprios)"""
    doc = nlp(texto)
    keywords = set()
    for token in doc:
        if token.pos_ in ['NOUN', 'PROPN']:
            keywords.add(token.lemma_.lower())
    return list(keywords)

def categorizar(keywords):
    """Cria categoria baseada em palavras-chave"""
    categorias = {
        'biologia espacial': ['microgravidade', 'celular', 'genético', 'imunológico', 'biologia', 'espacial'],
        'fisiologia humana no espaço': ['cardíaco', 'muscular', 'ossos', 'sistema nervoso', 'hormonal', 'humano'],
        'microgravidade e saúde': ['saúde', 'microgravidade', 'stress', 'radiacao', 'adaptacao'],
        'tecnologias para missões espaciais': ['tecnologia', 'sensor', 'dispositivo', 'instrumento', 'monitoramento'],
        'psicologia e comportamento no espaço': ['psicologia', 'comportamento', 'stress', 'astronauta', 'sono', 'mental']
    }
    for cat, palavras in categorias.items():
        if any(p in keywords for p in palavras):
            return cat
    return 'outras'

# ------------------------------
# INÍCIO DO PROCESSO
# ------------------------------

print("Carregando CSV...")
df = pd.read_csv(CSV_FILE)

print("Iniciando tradutor...")
translator = Translator()

print("Carregando modelo spaCy...")
nlp = spacy.load('pt_core_news_sm')  # Modelo em português

# Cria colunas extras
df['id'] = range(1, len(df)+1)
df['titulo_traduzido'] = ''
df['palavras_chave'] = ''
df['categoria'] = ''

print("Processando títulos e gerando palavras-chave...")
for idx, row in df.iterrows():
    titulo_original = str(row['Title'])  # Assumindo que a coluna se chama 'Title'
    
    # Traduzir título
    traducao = translator.translate(titulo_original, src='en', dest='pt').text
    df.at[idx, 'titulo_traduzido'] = traducao
    
    # Gerar palavras-chave
    texto_limpo = limpar_texto(traducao)
    keywords = gerar_palavras_chave(nlp, texto_limpo)
    df.at[idx, 'palavras_chave'] = ', '.join(keywords)
    
    # Categorizar
    categoria = categorizar(keywords)
    df.at[idx, 'categoria'] = categoria

print("Gerando arquivo SQL...")
with open(SQL_FILE, 'w', encoding='utf-8') as f:
    f.write(f"CREATE TABLE IF NOT EXISTS {TABLE_NAME} (\n")
    f.write("  id INT PRIMARY KEY,\n")
    f.write("  titulo_original TEXT,\n")
    f.write("  titulo_traduzido TEXT,\n")
    f.write("  palavras_chave TEXT,\n")
    f.write("  categoria TEXT\n")
    f.write(");\n\n")
    
    for idx, row in df.iterrows():
        id_ = row['id']
        titulo_original = str(row['Title']).replace("'", "''")
        titulo_traduzido = str(row['titulo_traduzido']).replace("'", "''")
        palavras_chave = str(row['palavras_chave']).replace("'", "''")
        categoria = str(row['categoria']).replace("'", "''")
        
        sql = f"INSERT INTO {TABLE_NAME} (id, titulo_original, titulo_traduzido, palavras_chave, categoria) "
        sql += f"VALUES ({id_}, '{titulo_original}', '{titulo_traduzido}', '{palavras_chave}', '{categoria}');\n"
        f.write(sql)

print(f"Concluído! SQL gerado em {SQL_FILE}")
