<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns="http://www.w3.org/1999/xhtml" >

	<xsl:output
		method="xml"
		version="1.0"
		encoding="utf-8"
		indent="yes"
		omit-xml-declaration="yes"/>

	<!-- list -->
	<xsl:template
		match="item"
		mode="textfield">
		<xsl:apply-templates select="question" mode="textfield" />
	</xsl:template>

	<!-- question -->
	<xsl:template
		match="question"
		mode="textfield">
		<p class='question'>
			<xsl:value-of disable-output-escaping="yes" select="."/>
		</p>
		<textarea>
			<xsl:attribute name="name">
				<xsl:value-of select="ancestor::item/@id" />
			</xsl:attribute>

				<xsl:attribute name="class">
						<xsl:text>variableTextField</xsl:text>
				</xsl:attribute>
				<xsl:value-of select="ancestor::item/@id" />

		</textarea>
	</xsl:template>

</xsl:stylesheet>