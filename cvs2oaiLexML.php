<?php
/**
 * "CVS to OAI-LexML", conversão de arquivos CSV genéricos (tabular plain text independente do separador) para OAI-LexML.
 * v1.0-2014-06-08 of https://github.com/ppKrauss/cvs2oaiLexML 
 * Usar em modo shell:
 * % php cvs2oaiLexML.php | more
 * % php cvs2oaiLexML.php lex1-isis1 <metadataIsis.txt > metadataLexml.xml
 */

/* ... DECISÕES DE PROJETO:
  Fixando convenções e recuperando dados brutos através de 
  https://github.com/ppKrauss/getcsv_stdOpenGov

  Avaliando se PHP está online ou no terminal, e pegando options por $_GET ou por
   http://www.php.net/manual/en/function.getopt.php

  Filtragem de dados dentro do getcsv_stdOpenGov: apenas para split de subcampos em arrays, vide convenções lex1-isis1.
  Filtragem final: com as registered functions no XSLT final (lembrando de usar namespace "fn" no lugar de "php" pois o XSLT pode ser reusado com Python, etc.)
  Reconstrução de dados: aparentemente o epigrafe (titulo da norma) não é oferecido, seria construido em função externa via data e numero.
  Validação: teste das URLs e verificação de consistencia (via regex) de epigrafe com codigo e data, etc. Posteriori, como sugerido no uso do kit LexML.

*/

/**
 * Conversão do CSV de entrada para LexML.
 */
function converter(
		$in_config,  		// configurações ('sep'=separador, 'universo'=projetos ou normas, 'x-tipo'=função para executar sub-conversão de string)
		&$out_handle,      	// handler do output
		$out_mode='lines'   // lines=linhas XML sem root, xml=com root e header, sql=comando INSERT das linhas
	) {
	print "\nOK converteu\n";
}

function printERR($msg) {
	file_put_contents('php://stderr', "$msg\n");	
}

/**
 * Efetua a transformação isis-to-OAI e as filtragens de campos. 
 */
function XSL_transf($xml,$xslfile='isis2oailex.xsl') {
	$xmldoc = DOMDocument::loadXML($xml);
	$xsldoc = DOMDocument::load($xslfile);
	// altera atributo do root (garantidamente elemento "xsl:transform")
	$xsldoc->documentElement->setAttribute('xmlns:fn','http://php.net/xsl');
	$proc = new XSLTProcessor();
	$proc->registerPHPFunctions();
	$proc->importStyleSheet($xsldoc);
	echo $proc->transformToXML($xmldoc);
}

?>