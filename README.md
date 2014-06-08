isis2oaile
==========

*"ISIS TO OAI-Lex"*, conversão de arquivos CSV de saída ISIS (da Câmara Municipal de São Paulo) para OAI-LexML.

# Apresentação #
Os procedimentos e algoritmos descritos a seguir, tem por objetivo garantir a reprodução fiel dos metadados do portal da Câmara Municipal de São Paulo, http://www.camara.sp.gov.br, no [portal LexML](http://www.lexml.gov.br/). 

O [projeto LexML](http://projeto.lexml.gov.br/) é uma das mais importantes iniciativas de transparência e interoperabilidade no Brasil.
Nele foi estabelecido um protocolo para a coleta de metadados de normas jurídicas brasileiras (Leis, Decretos, Portarias, etc.), com base no Protocolo [OAI-PMH](https://pt.wikipedia.org/wiki/OAI-PMH) (*Open Archives Information – Protocol for Metadata Harvest*), descrito na norma "[LexML Brasil - Parte 4 – Coleta de Metadados](http://projeto.lexml.gov.br/documentacao/Parte-4-Coleta-de-Metadados.pdf)", e com formato fixado por 

> http://projeto.lexml.gov.br/esquemas/oai_lexml.xsd

## Exemplo ilustrativo 

Apesar do ISIS permitir as mais diversas formas de saída em text/plain, foi escolhida uma variação específica do CSV já usado no "[Programa de Dados Abertos do Parlamento](http://www.camara.sp.gov.br/index.php?option=com_wrapper&view=wrapper&Itemid=219) (vide Portal da Câmara Municipal de São Paulo, seção Transparência/Dados Abertos),  arquivo "Produção Legislativa", descrito em [ARQ_BIBL.TXT](http://www2.camara.sp.gov.br/Dados_abertos/producaoLegislativa/ARQ_BIBL.TXT).

Como esse *dump ISIS* é uma convenção que ainda pode sofrer modificações, podemos avaliar um caso simplificado,

      TEXTO_NORMA;TIPO_NORMA;NUMERO_NORMA;DATA_NORMA;SITUACAO;EMENTA;DATA_PUBL_rep

Vejamos uma sequência de três normas,

      R0001-1892;Resolução;1;18/10/1892;1;Suspende o emplacamento das casas até ser revisto.;
      A0001-1893;ACTO;1;01/03/1893;1;Reorganiza o pessoal da Intendência Municipal.;
      ...
      R0001-1910;Resolução;1;30/04/1910;1;Dá provimento ao recurso interposto pela "Companhia Nacional de Tecidos de Juta".;
      L16008;Lei;16.008;05/06/2014;1;Dispõe sobre o reajustamento dos ... Profissionais de Educação.;PL;235;2014;
      AC127514;Ato da CMSP;1.275;05/06/2014;1;Abre Crédito Adicional Suplementar de R$ 6.000.000,00 de acordo com a Lei nº 15.950/2013.;
      
O primeiro campo, `TEXTO_NORMA`, contém o nome de arquivo usado no portal. Por exemplo a "Resolução 1 de 1982" está no arquivo `R0001-1892.pdf`, atualmente disponível na URL,

     http://camaramunicipalsp.qaplaweb.com.br/iah/fulltext/resolucoes/R0001-1892.pdf

Os demais campos são justamente os metadados requisitaodos pelo LexML.

O *dump ISIS* pode primeiramente ser transformadas num XML arbitrário através de ferramentas simples, como o [awk](https://pt.wikipedia.org/wiki/Awk):

     awk -F: 'BEGIN{FS=";"}{print "<item><arq>"$1"</arq><tipo>"$2"</tipo><num>"$3"</num><data>"$4"</data><ementa>"$6"</ementa></item>"}' < listaIsis.txt > listaItens.xml

Todavia, como o trabalho de conversão requer uma linguagem mais robusta, como Perl, Python ou PHP, optou-se por expressar todo o algoritmo dentro da mesma linguagem, sem pipes ou processos intermediários.

Também optou-se por não realizar armazenamento e análise intermediário, mas apenas efetuar a conversão, apenas "empacotar os dados".

# Procedimentos

  1. Obter arquivo completo e atualizado da base ISIS (pode resultar em dezenas Mb). Supor `dump.txt`
  2. rodar `php isis2oailex.php < dump.txt > dump.xml`
  3. Encaminhar `dump.xml.zip` ao LexML.

# Descrição do algoritmo

Conforme exemplificado pela linha `awk` acima, a etapa mais simples é a transformação do arquivo CSV em XML. Todavia, para a obtenção de todos os metadados é necessário acessar campos do ISIS que se encontram codificados com sinais de "<" e ">", e com subcampos separados por "%". Assim algumas filtragens de string são necessárias.

Esse primeiro XML, denominado *formato intermediário* se presta apenas para a submissão dos dados a uma [transformação XSLT](https://en.wikipedia.org/wiki/XSLT), ou seja,  a um parser expresso por linguagem padronizada. Como uma alternativa ao pré-parsing é o tratamento de strings através do próprio XSLT, mas o XSLT1 (padrão de 1999 menos expressivo que o XSLT2 porém mais leve e mais difundido)  optamos por usar [XSLT1 com registerFunctions](https://en.wikibooks.org/wiki/PHP_Programming/XSL/registerPHPFunctions), tudo em ambiente PHP. 

Temos aparentemente dois softwares de processamento, um em PHP outro em XSLT1, mas o *core* está no arquivo `isis2oailex.xsl`, que se encontra documentado, e, por ser uma linguagem funcional, é praticamente auto-explicativo.

# Outros links e referências 

* [projeto LexML na Wikipedia](https://pt.wikipedia.org/wiki/LexML_Brasil) 
* ...
* ...

# Outras propostas 

Criar base de dados intermediária para serviços de revisão, homologação e monitoramento das atualizações. 

