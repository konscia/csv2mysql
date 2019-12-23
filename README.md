#Conversor CSV to MySQL
Este conversor transforma um csv em uma tabela do MySQL 
usando como nome das colunas o cabeçalho do CSV. 

É útil quando existe preferência por explorar um csv com MySQL 
ao invés de linha de comando ou código.

## Configuração
Copie o arquivo de configuração removendo o ".dist"

```
cp config.php.dist config.php
```

Edite o conteúdo com usuári, senha do banco de dados e database.

## UTF8
Os arquivos CSV devem estar em formato UTF-8. 
Se precisar converter sugiro o comando abaixo

```
iconv -f ISO-8859-1 -t UTF-8 nome.txt -o nome.utf8.txt
```

## Exemplo de uso
Na pasta "sample" há um arquivo de exemplo que pode ser usado 
para um primeiro teste. O arquivo contém dados reais dos 
candidatos à eleição de 2012 no Acre.

Execute o comando na raiz do projeto. a base de dados deve estar previamente criada.

* O primeiro parâmetro é o caminho do arquivo a importar
* O segundo é o nome da tabela a ser criada
* O terceiro é o número de registros a serem inseridos a cada insert (quanto mais registros, mais rápido importa, mas dependendo da configuração do tamanho do buffer para envio de comandos ao MySQL você pode ter que colocar um tamanho menor);
* O quarto é o separador das colunas 

```
user@pc:/www/csv2mysql$ php import-csv.php sample/candidatos_eleicoes_2012_acre.utf8.txt candidatos_2012 1000 ";"
Criando tabela candidatos_2012
Tentando inserir registros de 1 até 1001
Inseridos 1000 registros
Tentando inserir registros de 1001 até 2001
Inseridos 1000 registros
Tentando inserir registros de 2001 até 3001
Inseridos 343 registros
Fim
```
