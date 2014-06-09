cvs2oaiLexML
============

*"CVS to OAI-LexML"*, conversão de arquivos CSV genéricos (*tabular plain text* independente do separador) para OAI-LexML.

# Sinopse dos procedimentos

  1. Obter arquivo completo e atualizado da base ISIS (pode resultar em dezenas Mb). Supor `dump.txt`
  2. configurar e rodar `php isis2oailex.php < dump.txt`
  3. configurar e rodar o "kit provedor de dados": ver  [lexml-toolkit](https://github.com/lexml/lexml-toolkit), [lexml-coleta-validador](https://github.com/lexml/lexml-coleta-validador) e [seu guia](http://projeto.lexml.gov.br/documentacao/LexML_Brasil-Parte_4a-Kit_Provedor_de_Dados%20v.pdf).

# Apresentação #

O [portal LexML](http://www.lexml.gov.br/), conhecido como "Google das normas brasileiras", unifica a busca e o acesso a normas jurídicas brasileiras, e, no mesmo domínio, `lexml.gov.br`, com igual compromisso de persistência e estabilidade, oferece serviços para  resolução de URNs das normas. São mecanismos fundamentais para o acesso, inter-link, relacionamento, comparação, certificação, e dezenas de outros procedimentos relativos ao sistema legislativo.

Os algoritmos descritos a seguir, tem por objetivo garantir a conversão fiel dos metadados das normas jurídicas de um municipio  (Leis, Decretos, Portarias, etc.), quando dispostos em arquivo [tipo CVS](https://en.wikipedia.org/wiki/Comma-separated_values) (com separador flexibilizado e header engessado), para o formato [OAI-LexML](http://projeto.lexml.gov.br/esquemas/oai_lexml.xsd); e, opcionalmente, para uma base de dados passível de ser validada pelo [software do "kit provedor de dados"](http://projeto.lexml.gov.br/documentacao/LexML_Brasil-Parte_4a-Kit_Provedor_de_Dados%20v.pdf).  

Historicamente dois exemplos típicos caracterizam o problema em contextos bem distintos:
* Prefeitura Municipal de Garuva (SC): em 2010 listou em planilha eletrônica (OpenOffice) suas 3.974 leis para então serem gravadas como CVS, e depois convertidas por software e disponibilizadas pelo LexML.
* Câmara Municipal de São Paulo: em 2014 o portal http://www.camara.sp.gov.br passou a gerar e tornar acessíveis os registros completos da sua base de metadados das normas e dos projetos-de-norma do município, quase 200Mb (centenas de milhares de normas). Tendo sua origem num antigo sistema ISIS, a forma mais prática de exportá-los foi o "arquivo tipo CSV". O presente projeto Github foi então concebido para a sua exportação, e aproveitando a experiência de Garuva.

O [projeto LexML](http://projeto.lexml.gov.br/) é uma das mais importantes iniciativas de transparência e interoperabilidade no Brasil.
Nele foi estabelecido um protocolo para a coleta de metadados de normas jurídicas brasileiras (Leis, Decretos, Portarias, etc.), com base no Protocolo [OAI-PMH](https://pt.wikipedia.org/wiki/OAI-PMH) (*Open Archives Information – Protocol for Metadata Harvest*), descrito na norma "[LexML Brasil - Parte 4 – Coleta de Metadados](http://projeto.lexml.gov.br/documentacao/Parte-4-Coleta-de-Metadados.pdf)", e com esquema (dialeto de  XML) fixado por http://projeto.lexml.gov.br/esquemas/oai_lexml.xsd

## Exemplo ilustrativo 

Apesar do ISIS permitir as mais diversas formas de saída em text/plain, foi escolhida uma variação específica do CSV já usado no "[Programa de Dados Abertos do Parlamento](http://www.camara.sp.gov.br/index.php?option=com_wrapper&view=wrapper&Itemid=219) (vide Portal da Câmara Municipal de São Paulo, seção Transparência/Dados Abertos),  arquivo "Produção Legislativa", descrito em [ARQ_BIBL.TXT](http://www2.camara.sp.gov.br/Dados_abertos/producaoLegislativa/ARQ_BIBL.TXT).

Como esse *dump ISIS* é uma convenção que ainda pode sofrer modificações, podemos avaliar um caso simplificado,

      arq;tipo;num;dataAss;situacao;ementa;dataPub

Vejamos uma sequência de três normas,

      R0001-1892;Resolução;1;18/10/1892;1;Suspende o emplacamento das casas até ser revisto.;
      A0001-1893;ACTO;1;01/03/1893;1;Reorganiza o pessoal da Intendência Municipal.;
      ...
      R0001-1910;Resolução;1;30/04/1910;1;Dá provimento ao recurso interposto pela "Companhia Nacional de Tecidos de Juta".;
      L16008;Lei;16.008;05/06/2014;1;Dispõe sobre o reajustamento dos ... Profissionais de Educação.;PL;235;2014;
      AC127514;Ato da CMSP;1.275;05/06/2014;1;Abre Crédito Adicional Suplementar de R$ 6.000.000,00 de acordo com a Lei nº 15.950/2013.;
      
O primeiro campo, `arq`, contém o nome de arquivo usado no portal. Por exemplo a "Resolução 1 de 1982" está no arquivo `R0001-1892.pdf`, atualmente disponível  em  http://camaramunicipalsp.qaplaweb.com.br/iah/fulltext/resolucoes/R0001-1892.pdf

Os demais campos são justamente os metadados requisitaodos pelo LexML: tipo de norma (Lei, Decreto, etc.), número público oficial da norma, data de assinatura da norma,  situação (vigor), ementa da norma, data de publicação no Diário Oficial do Município, e outros. 

O *dump ISIS* pode primeiramente ser transformadas num XML arbitrário através de ferramentas simples, como o [awk](https://pt.wikipedia.org/wiki/Awk):

     awk -F: 'BEGIN{FS=";"}{print "<item><arq>"$1"</arq><tipo>"$2"</tipo><num>"$3"</num><dataAss>"$4"</dataAss><ementa>"$6"</ementa></item>"}' < listaIsis.txt > listaItens.xml

Todavia, como o trabalho de conversão requer uma linguagem mais robusta, como Perl, Python ou PHP, optou-se por expressar todo o algoritmo dentro da mesma linguagem, sem pipes ou processos intermediários.

Também optou-se por não realizar armazenamento e análise intermediários, mas apenas "empacotar os dados".

----

# Descrição do algoritmo

Conforme exemplificado pela linha `awk` acima, a etapa mais simples é a transformação do arquivo CSV em XML. Todavia, para a obtenção de todos os metadados é necessário acessar campos do ISIS que se encontram codificados com sinais de "<" e ">", e com subcampos separados por "%". Assim algumas filtragens de string são necessárias.

Esse primeiro XML, denominado *formato intermediário* se presta apenas para a submissão dos dados a uma [transformação XSLT](https://en.wikipedia.org/wiki/XSLT), ou seja,  a um parser expresso por linguagem padronizada. Como uma alternativa ao pré-parsing é o tratamento de strings através do próprio XSLT, mas o XSLT1 (padrão de 1999 menos expressivo que o XSLT2 porém mais leve e mais difundido)  optamos por usar [XSLT1 com registerFunctions](https://en.wikibooks.org/wiki/PHP_Programming/XSL/registerPHPFunctions), tudo em ambiente PHP. 

Temos aparentemente dois softwares de processamento, um em PHP outro em XSLT1, mas o *core* está no arquivo `isis2oailex.xsl`, que se encontra documentado, e, por ser uma linguagem funcional, é praticamente auto-explicativo.

# Convenções sugeridas e adotadas
Duas convenções principais foram fixadas, como proposta às prefeituras: 
 1. Recupoeração de dados brutos do município: através da classe [getcsv_stdOpenGov](https://github.com/ppKrauss/getcsv_stdOpenGov), onde podemos fixar parte das convenções, tais nomes dos campos relativos aos metadados das normas. Foi requisitado também dispor o CVS em UTF-8 com primeira linha contendo os nomes de campo.
 2. XML intermediário (baseado nos nomes dos campos convencionados no item anterior).

 Além das convenções para a recuperação de dados brutos, foram sugeridas convenções para a Câmara no sentido de oferecer ao público URLs mais consistentes e perenes, usando ao invés do domínio "camaramunicipalsp.qaplaweb.com.br", o domínio "www.camara.sp.gov.br" na exposição dos PDFs e demais conteúdos normativos originais.

## XML intermediário

Apenas os seguintes nomes de campo podem ser utilizados na primeira linha:
* arq: nome de arquivo ou indentificador da norma
* tipo: tipo de norma (Lei, Decreto, etc.)
* num: número público oficial da norma
* dataAss: data de assinatura da norma,  ), . 
* situacao: situação (vigor no presente)
* ementa: ementa da norma
* dataPub: data de publicação no Diário Oficial do Município
... nenhum outro nome é válido (alterar o presente documento se precisar).

## Opções de transferência
... XML final para SQL ou XML ...

## Projetos de Lei
O LexML, apesar de priorizar as normas, aceita também "projetos de norma". Por exemplo o projeto [PL-2788 (urn:lex:br:camara.deputados:projeto.lei;pl:2011;2788)](http://www.lexml.gov.br/urn/urn:lex:br:camara.deputados:projeto.lei;pl:2011;2788) já devidamente registrado, que deu origem à [Lei 11705 (urn:lex:br:federal:lei:2008;11705)](http://www.lexml.gov.br/urn/urn:lex:br:federal:lei:2008-06-19;11705). Para expresar esse relacionamento, os metadados devem incluir uma tag `<Relacionamento tipo="sucessor.logico.de">` com a URN do projeto de lei.

----

# Outros links e referências 
* https://github.com/lexml/
  * https://github.com/lexml/lexml-toolkit
  * https://github.com/lexml/lexml-coleta-validador

* [Descrição do software complexo do "kit provedor de dados"](http://projeto.lexml.gov.br/documentacao/LexML_Brasil-Parte_4a-Kit_Provedor_de_Dados%20v.pdf) 
* Outro [software de onde pode-se resgatar talvez detalhes como "montagem da epigrafe"](http://sapl.googlecode.com/svn/trunk/SAPLTool.py)
* [projeto LexML na Wikipedia](https://pt.wikipedia.org/wiki/LexML_Brasil) 
* ...

# Proposta complementar

Base de dados intermediária para serviços de revisão, homologação e monitoramento das atualizações... Precisaria ser gerenciada por entidade responsável pela prestação de serviços e tutela das bases de dados, principalmente para suporte aos municípios que ainda não atingiram a [maturidade ou autonomia](http://www.consultas.governoeletronico.gov.br/ConsultasPublicas/contribuicao.do;jsessionid=C13E5697FF43725DB0020A8BD15E77AB?acao=exibir&id=831) suficientes para proverem sozinhos os dados ao LexML.



