<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns="http://www.w3.org/1999/xhtml">

<!--	<xsl:output
		method="html"
		version="4.0"
		indent="yes"
		doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN"
		doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"
		media-type="text/html"/>-->

	<xsl:include href="section.xsl"/>
	<xsl:include href="textfield.xsl"/>
	<xsl:include href="information.xsl"/>
	<xsl:include href="list.xsl"/>
	<xsl:include href="matrix.xsl"/>
	<xsl:include href="form.xsl"/>
	<xsl:include href="layout.xsl"/>

	<!-- itemGroup -->
	<xsl:template
		name="itemGroup"
		match="itemGroup">
		<div id="container">
			<div id="info_dialog">
				<span id="info_dialog_close">[ <span class="close"></span> ]</span>
			</div>
			<div id="menuDialog">
				<div class="headerMenuDialog">
					<span id="menuDialogClose">[ <span class="close"></span> ]</span>
				</div>
<!--				<div class="contentMenuDialog">
					<ul id="activities"></ul>
				</div>-->
			</div>

			<xsl:if test="@layout='simpleMultipleChoiceRadioButton'
					   or @layout='simpleMultipleChoiceCheckbox'
					   or @layout='simpleFieldsList'">
				<xsl:apply-templates select="item" mode="list" />
			</xsl:if>
			<xsl:if test="@layout='complexMultipleChoiceRadioButton'
					   or @layout='complexMultipleChoiceCheckbox'
					   or @layout='complexFieldsList'
					   or @layout='complexCompleteFields'
					   or @layout='slider'">
				<xsl:apply-templates select="item" mode="matrix" />
			</xsl:if>
			<xsl:if test="@layout='information'">
				<xsl:apply-templates select="item" mode="information" />
			</xsl:if>
			<xsl:if test="@layout='textfield'">
				<xsl:apply-templates select="item" mode="textfield" />
			</xsl:if>
			<xsl:if test="@layout='section'">
				<xsl:apply-templates select="item" mode="section" />
			</xsl:if>
			<xsl:if test="@layout='rules'">
				<xsl:apply-templates select="item" mode="rules" />
			</xsl:if>
		</div>
	</xsl:template>

	<xsl:template name="headSection">
		<link class="head" rel="stylesheet" type="text/css" href="css/section.css" media="screen" />
	</xsl:template>

	<xsl:template name="headInformation">
		<link class="head" rel="stylesheet" type="text/css" href="css/information.css" media="screen" />
	</xsl:template>

	<xsl:template name="headSlider">
		<link class="head" rel="stylesheet" type="text/css" href="lib/jquery/ui/themes/base/jquery.ui.all.css" media="screen" />
	</xsl:template>
</xsl:stylesheet>
