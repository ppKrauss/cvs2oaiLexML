<?php
/**
 * "CVS to OAI-LexML", conversão de arquivos CSV genéricos (tabular plain text independente do separador) para OAI-LexML.
 * v1.0-2014-06-08 of https://github.com/ppKrauss/cvs2oaiLexML 
 * Usar em modo terminal:
 * % php cvs2oaiLexML.php > arquivo.xml
 */

// // // // // // // // //
// CONFIGURAR COM ATENCAO:
	$CHARSET = 'UTF-8'; // MUDAR PARA ISO SE PRECISAR, O CORRETO E-PING É OFERECER TXT UTF8
	$FILE = 'ARQ_BIBL.TXT';
	$nmax = 0;   // usar por ex. 100 para teste.
// FIM CONFIGS


$h = fopen($FILE,'r');
$FC = array('tipo','numero','data','promovente','ementa','assunto','tipo_promulgacao','numero_promulgacao','data_promulgacao','','','','','','','','','','','');
	//       0      1        2      ..
$tipos = array(
	'PL'=>'Projeto de Lei',
	'PDL'=>'Projeto de Decreto Legislativo',
	'PLO'=>'Projeto de Emenda a Lei Orgânica',
	'PR'=>'Projeto de Resolução',
	'MOC'=>'Moção',
	'RPP'=>'Requerimento P com Processo',
	'RDP'=>'Requerimento D com Processo',
	'IND'=>'Indicação',
	'REC'=>'Recurso',
	'RDS'=>'Requerimento D sem Processo',
	'DOCREC'=>'Documento Recebido',
);

$tipos_urn = array(
	'PL' =>'projeto.lei',
	'PDL'=>'projeto.decreto;legislativo',
	'PLO'=>'projeto.emenda.lei.organica',
	'PR' =>'projeto.resolucao',
);

$n=0;
print  <<<EOB
<?xml version="1.0" encoding="$CHARSET" standalone="yes" ?>
<metadata>
EOB;

while( !feof($h) && (!$nmax || $n<$nmax) ) {
	$n++;
	$lin = fgetcsv($h,0,'#'); // 0 ou max. length por performance
	if (  $lin[1]>0 && !in_array($lin[0],array('IND','MOC','RPP','RDP','REC','RDS','DOCREC'))  ) {
		if ( preg_match('|(\d\d).(\d\d).(\d\d\d\d)|',$lin[2],$m) ) {
			$ano=$m[3];
			$data_iso = "$m[3]-$m[2]-$m[1]";
		}else {
			$ano='';
			$data_iso = $lin[2];
		}

		$tipo = $lin[0];
		$num = $lin[1];
		$tipo_urn = $tipos_urn[$tipo];
		$tipo_ext = $tipos[$tipo];
		$ementa = str_replace('%'," ",$lin[4]);

		$num4 = str_pad($num,4,'0',STR_PAD_LEFT);
		$URL = "http://camaramunicipalsp.qaplaweb.com.br/iah/fulltext/projeto/$tipo$num4-$ano.pdf";
		// se precisar: $URL = str_replace('&','&amp;',$URL);
		// se precisar: $RELACIONAMENTO="\n\t<Relacionamento tipo=\"sucessor.logico.de\">...</Relacionamento>";
		//              mas depende de armazenamento temporário em hash ou base dados.
		$RELACIONAMENTO='';

		print  <<<EOB

<LexML xmlns="http://www.lexml.gov.br/">
	<Item formato="text/html">
	$URL
	</Item>
	<DocumentoIndividual>
	urn:lex:br;sao.paulo;sao.paulo:municipal:$tipo_urn:$data_iso;$lin[1]
	</DocumentoIndividual>
	<Epigrafe>$tipo_ext núm. $num de $lin[2]</Epigrafe>
	<Ementa>$ementa</Ementa>$RELACIONAMENTO
</LexML>
EOB;


	} // if valido
} // loop file
fclose($h);
print "\n\n</metadata>\n";

?>