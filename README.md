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

Como é uma convenção que ainda pode sofrer modificações, podemos avaliar um caso simplificado,

      TEXTO_NORMA;TIPO_NORMA;NUMERO_NORMA;DATA_NORMA;SITUACAO;EMENTA;DATA_PUBL_rep

Vejamos uma sequência de três normas,

      R0001-1892;Resolução;1;18/10/1892;1;Suspende o emplacamento das casas até ser revisto.;
      A0001-1893;ACTO;1;01/03/1893;1;Reorganiza o pessoal da Intendência Municipal.;
      ...
      R0001-1910;Resolução;1;30/04/1910;1;Dá provimento ao recurso interposto pela "Companhia Nacional de Tecidos de Juta".;
      L16008;Lei;16.008;05/06/2014;1;Dispõe sobre o reajustamento dos ... Profissionais de Educação.;PL;235;2014;
      AC127514;Ato da CMSP;1.275;05/06/2014;1;Abre Crédito Adicional Suplementar de R$ 6.000.000,00 de acordo com a Lei nº 15.950/2013.;
      
Elas podem primeiramente ser transformadas num XML arbitrário através de ferramentas simples, como o [awk](https://pt.wikipedia.org/wiki/Awk):

     awk -F: 'BEGIN{FS=";"}{print "<item><arq>"$1"</arq><tipo>"$2"</tipo><num>"$3"</num><data>"$4"</data><ementa>"$6"</ementa></item>"}' < listaIsis.txt > listaItens.xml

Todavia, como o trabalho de conversão requer uma linguagem mais robusta, como Perl, Python ou PHP, optou-se por expressar todo o algoritmo dentro da mesma linguagem, sem pipes ou processos intermediários.

Também optou-se por não realizar armazenamento e análise intermediário, mas apenas efetuar a conversão, apenas "empacotar os dados".

# Procedimentos de preparo
...

# Rotina de atualização
...

# Outros links e referências 

* [projeto LexML na Wikipedia](https://pt.wikipedia.org/wiki/LexML_Brasil) 
* ...
* ...

# Outras propostas 

Criar base de dados intermediária para serviços de revisão, homologação e monitoramento das atualizações. 

