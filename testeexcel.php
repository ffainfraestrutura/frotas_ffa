<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Exportar para XLSX</title>
  <!-- Inclua as bibliotecas SheetJS -->
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>
</head>
<body>
  <!-- Sua tabela HTML -->
  <table id="minhaTabela">
    <tr>
      <th>Nome</th>
      <th>Idade</th>
      <th>País</th>
    </tr>
    <tr>
      <td>João</td>
      <td>25</td>
      <td>Brasil</td>
    </tr>
    <!-- Adicione mais linhas conforme necessário -->
  </table>

  <!-- Botão para exportar a tabela -->
  <button onclick="exportarParaXLSX()">Exportar para XLSX</button>

  <!-- Seu script JavaScript -->
  <script>
    function exportarParaXLSX() {
      /* Obtenha os dados da tabela */
      var tabela = document.getElementById('minhaTabela');
      var dados = XLSX.utils.table_to_sheet(tabela);

      /* Crie um objeto de pasta de trabalho e adicione a folha de dados a ela */
      var wb = XLSX.utils.book_new();
      XLSX.utils.book_append_sheet(wb, dados, 'Planilha1');

      /* Salve o arquivo XLSX */
      XLSX.writeFile(wb, 'saida.xlsx');
    }
  </script>
</body>
</html>
