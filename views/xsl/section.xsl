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
		mode="section">
		<div id="section">
			<xsl:choose>
				<xsl:when test="count(questionDescription/following-sibling::question)&gt;0">
					<xsl:apply-templates select="questionDescription" mode="section" />
					<xsl:apply-templates select="question" mode="section" />
				</xsl:when>

				<xsl:otherwise>
					<xsl:apply-templates select="question" mode="section" />
					<xsl:apply-templates select="questionDescription" mode="section" />
				</xsl:otherwise>
			</xsl:choose>
		</div>
	</xsl:template>

	<!-- question -->
	<xsl:template
		match="question"
		mode="section">
		<p id='titre_section'>
			<xsl:value-of disable-output-escaping="yes" select="."/>
		</p>
	</xsl:template>

	<!-- questionDescription -->
	<xsl:template
		match="questionDescription"
		mode="section">
		<p id='desc_section'>
			<xsl:call-template name="question_description" />
			<xsl:value-of select="." disable-output-escaping="yes" />
		</p>
	</xsl:template>

</xsl:stylesheet>