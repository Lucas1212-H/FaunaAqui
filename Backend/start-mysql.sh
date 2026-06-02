#!/bin/bash

# 1. Inicia o MySQL em segundo plano usando o entrypoint oficial da imagem
docker-entrypoint.sh mysqld &

# 2. Aguarda o MySQL subir totalmente na porta 3306
echo "Aguardando o MySQL iniciar..."
while ! timeout 1 bash -c "cat < /dev/null > /dev/tcp/127.0.0.1/3306" 2>/dev/null; do
    sleep 2
done
echo "MySQL está pronto para conexões!"

# 3. Cria um falso servidor HTTP na porta exigida pelo Render ($PORT) usando Python
echo "Iniciando falso servidor HTTP na porta $PORT para o validador do Render..."
python3 -c "
import os, socket
s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
s.bind(('0.0.0.0', int(os.environ['PORT'])))
s.listen(1)
while True:
    conn, _ = s.accept()
    conn.recv(1024)
    conn.sendall(b'HTTP/1.1 200 OK\r\nContent-Type: text/plain\r\n\r\nOK')
    conn.close()
"