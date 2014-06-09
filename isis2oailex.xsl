<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>
<!-- ============================================================= -->
<!--  MODULO:    isis-to-OAI                                       -->
<!--  VERSAO:    1.00           DATA: 2014/06/08                   -->
<!-- ============================================================= -->

<xsl:transform version="1.0" exclude-result-prefixes="php"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:fn="http://php.net/xsl"
><!-- no software vai fazer efetivamente documentElement.setAttribute da linguagem desejada como fn-->
<xsl:output encoding="UTF-8" method="html" version="1.0" indent="yes" omit-xml-declaration="yes" />

	<xsl:template match="name" priority="9">
		<xsl:choose>
		  <xsl:when test="surname">
		  	<span class="surname"><xsl:value-of select="surname"/></span>&#160;
			<xsl:choose>
			  <xsl:when test="given-names-abbrev"><span class="given-names-abbrev"><xsl:value-of select="given-names-abbrev"/></span></xsl:when>
			  <xsl:otherwise><span class="given-names"><xsl:value-of select="given-names"/></span></xsl:otherwise>
			</xsl:choose>
		  </xsl:when>
		  <xsl:otherwise>
			<xsl:choose>
			  <xsl:when test="given-names-abbrev"><span class="given-names-abbrev"><xsl:value-of select="given-names-abbrev"/></span></xsl:when>
			  <xsl:when test="given-names"><span class="given-names"><xsl:value-of select="given-names"/></span></xsl:when>
			  <xsl:otherwise><span class="name"><xsl:value-of select="."/></span></xsl:otherwise><!-- unico possivel ? -->
			</xsl:choose>
		  </xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="mixed-citation"><xsl:apply-templates/></xsl:template>

	<xsl:template match="x-page-range">:<span class="pages"><xsl:value-of select="."/></span></xsl:template>
	<xsl:template match="volume|number|year|issn|source|source-abbrev"><span class="{name()}"><xsl:value-of select="."/></span></xsl:template>

	<xsl:template match="pub-id[@pub-id-type!='doi' and @pub-id-type!='pmid']">
		&#160;ID? <xsl:value-of select="."/>&#160;
	</xsl:template>
	<xsl:template match="pub-id[@pub-id-type='doi']">
		&#160;DOI:<a data-pidtype="doi" href="http://dx.doi.org/{.}" target="_blank"><xsl:value-of select="."/></a>&#160;
	</xsl:template>
	<xsl:template match="pub-id[@pub-id-type='pmid']">
		&#160;pubmed-ID <a data-pidtype="pubmed" href="http://www.ncbi.nlm.nih.gov/pubmed/?term={.}" target="_blank"><xsl:value-of select="."/></a>&#160;
	</xsl:template>

	<xsl:template match="article-title">
		<xsl:if test="@xml:lang"><small>(<xsl:value-of select="@xml:lang"/>)</small>&#160;</xsl:if>
		     <span class="article-title"><xsl:value-of select="."/></span><xsl:text> </xsl:text>
	</xsl:template>

	<xsl:template match="/">
	<html>	
	<head><!-- link type="text/css" rel="stylesheet" href="./SJATS_xcolor.css"/ -->
		<style>
		body {
			font-family:Arial, Helvetica;
			font-size: 10pt;			
			}
		p.ref {
			padding-left:60px; 
			background: url('./css/img/ref-chk.png');
			background-position: 0 0    !important;
			background-repeat:no-repeat !important;
			background-color: #DBE7FC;
			padding-bottom: 0.6em;
			padding-top: 0.3em;
			border-top: dashed;
			border-top-width: 1px;
			border-top-color:#89A;
		}
		.labelXref 			{
			background: #FF3;
			box-shadow: 0px 3px 3px #F90;
			}

		span.article-title{
			color:#464;
		}
		span.etc{
			color:#857;
		}
		.candidata {
			background-color: #DBDCB9;
			margin-left:  8px;
			margin-right: 8px;
		}
		.refret {
			background-color: #DBE7FC;
			font-size: 10pt;
		}
		</style>
	</head>

	<body>
		<xsl:for-each select="//ref">
		<div class="refret">
			<p class="ref"><b class="labelXref"><xsl:value-of select="label"/></b> <xsl:apply-templates select="mixed-citation"/></p>
			<xsl:for-each select="element-citation">
			<p class="candidata"><b>(<xsl:value-of select="@filtered-score"/>%)</b>&#160;<xsl:value-of select="@publication-type"/> 
				na <b><xsl:value-of select="@server"/>:</b>
				<br/>
				<xsl:for-each select=".//person-group/name"><xsl:apply-templates select="."/><xsl:if test="position()!=last()"><xsl:text>, </xsl:text></xsl:if></xsl:for-each><xsl:text>: </xsl:text> 
				<xsl:apply-templates select="article-title"/><xsl:text> </xsl:text>
				<xsl:choose>
				  <xsl:when test="source">
				  		<span class="source"><i><xsl:value-of select="source"/></i></span><xsl:choose>
						  <xsl:when test="issn and source-abbrev"><xsl:text> </xsl:text>
						  	(<xsl:apply-templates select="source-abbrev"/>, ISSN <xsl:apply-templates select="issn"/>)</xsl:when>
						  <xsl:when test="issn"><xsl:text> </xsl:text>(ISSN <xsl:apply-templates select="issn"/>)</xsl:when>
						  <xsl:when test="source-abbrev"><xsl:text> </xsl:text>(<xsl:apply-templates select="source-abbrev"/>)</xsl:when>
						</xsl:choose><xsl:text>. </xsl:text>
				  </xsl:when>
				  <xsl:otherwise>
					<xsl:choose>
					  <xsl:when test="issn and source-abbrev">
					  	 <i><xsl:apply-templates select="source-abbrev"/></i> (ISSN <xsl:apply-templates select="issn"/>)</xsl:when>
					  <xsl:when test="issn">ISSN <xsl:apply-templates select="issn"/></xsl:when>
					  <xsl:when test="source-abbrev"><i><xsl:apply-templates select="source-abbrev"/></i></xsl:when>
					</xsl:choose><xsl:text>. </xsl:text>
				  </xsl:otherwise>
				</xsl:choose>
				<xsl:apply-templates select="year"/>;
				<xsl:apply-templates select="volume"/><xsl:if test="number">(<xsl:apply-templates select="number"/>)</xsl:if><xsl:apply-templates select="x-page-range"/>.
				<xsl:apply-templates select="pub-id"/>
			</p>
			</xsl:for-each><!-- element-->
		</div>
		</xsl:for-each><!-- ref-->
	</body>
	
	</html>		
</xsl:template>

<xsl:template match="b|strong|bold"><b><xsl:apply-templates/></b></xsl:template>
<xsl:template match="i|emph|italic"><i><xsl:apply-templates/></i></xsl:template>
<xsl:template match="text()"><xsl:value-of select="."/></xsl:template>


</xsl:transform>