<?php
/**
 * "CVS to OAI-LexML", conversão de arquivos CSV genéricos (tabular plain text independente do separador) para OAI-LexML.
 * v1.0-2014-06-08 of https://github.com/ppKrauss/cvs2oaiLexML 
 * Usar em modo shell:
 * % php cvs2oaiLexML.php | more
 * % php cvs2oaiLexML.php isis-sampa <metadataIsis.txt > metadataLexml.xml
 */

if ( !isset($argv[0]) || !isset($argv[1]) ) {
	printERR("Rodar offline no terminal, usar como primeiro argumento nome da convenção (ex. isis-sampa).");
} else {
	$convencao = 'isis-sampa';
	$file = 'php://stdin';
	if (isset($argv[1]) && isset($argv[2])) {
		$convencao = $argv[1];
		$file = $argv[2];
	}
	switch ($convencao) {
		case 'isis-sampa0':
		case 'isis-sampa':
			$in_configs = array(
				'sep'=>($convencao=='isis-sampa0')? ';': '#', 
				'x'=>'', 
				'y'=>'',
			);
			break;
		case 'planilha-garuva':
			// old 2010, recuperar
			$in_configs = array(
				'sep'=>';', 
				'x'=>'', 
				'y'=>'',
			);
			break;
		default:
			die("ERRO 1: convenção '$convencao' não implementada\n");
			break;
	}
	$out_mode = (isset($argv[3]))? $argv[3]: 'lines';
	if (isset($argv[1])){
			print "VAI PROCESSAR COM OS SEGUINTES PARAMETRIOS:\n";
			print "input file: $file;   convenção: $convencao\n";
			print "Configs da convenção:\n";
			var_dump($in_configs);
			die("\n");
	} else {
		$handle = fopen($file, "r");
		if ($handle===FALSE)
			printERR("ERRO 2: não conseguiu abrir arquivo");
		else {
			print converter($in_configs,$handle,$out_mode);
			fclose($handle);		
		}
	} // else

} // func

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
?>