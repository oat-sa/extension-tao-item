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
		mode="list">

		<xsl:choose>
			<xsl:when test="count(questionDescription/following-sibling::question)&gt;0">
				<xsl:apply-templates select="questionDescription" mode="list">
					<xsl:with-param name="pos" select="'top'" />
				</xsl:apply-templates>
				<xsl:apply-templates select="question" mode="list">
					<xsl:with-param name="pos" select="'bottom'" />
				</xsl:apply-templates>
			</xsl:when>

			<xsl:otherwise>
				<xsl:apply-templates select="question" mode="list">
					<xsl:with-param name="pos" select="'top'" />
				</xsl:apply-templates>
				<xsl:apply-templates select="questionDescription" mode="list">
					<xsl:with-param name="pos" select="'bottom'" />
				</xsl:apply-templates>
			</xsl:otherwise>
		</xsl:choose>

		<xsl:apply-templates select="instruction" mode="list" />
		<xsl:apply-templates select="responses" mode="list" />
	</xsl:template>

	<!-- question -->
	<xsl:template
		match="question"
		mode="list">
		<xsl:param name="pos" />
		<xsl:variable name='value'>
			<xsl:value-of disable-output-escaping="yes" select="."/>
		</xsl:variable>
		<xsl:if test="$value!=''">
			<p class="question">
				<xsl:if test="$pos='top'">
					<xsl:attribute name="class">
						<xsl:text>question noMarginTop</xsl:text>
					</xsl:attribute>
				</xsl:if>
				<xsl:value-of disable-output-escaping="yes" select="."/>
			</p>
		</xsl:if>
	</xsl:template>

	<!-- instruction -->
	<xsl:template
		match="instruction"
		mode="list">
		<p>
			<xsl:call-template name="instruction" />
			<xsl:value-of disable-output-escaping="yes" select="."/>
		</p>
	</xsl:template>

	<!-- responses -->
	<xsl:template
		match="responses"
		mode="list">
		<table class="list_table">
			<xsl:variable name='footer'>
				<xsl:value-of select="ancestor::itemGroup/footer" />
			</xsl:variable>
			<xsl:if test='$footer!=""'>
				<xsl:apply-templates select="ancestor::itemGroup/footer" mode="layout" />
			</xsl:if>
			<xsl:if test="ancestor::itemGroup/@layout!='simpleFieldsList' and ancestor::itemGroup/@layout!='complexFieldsList'">
				<thead>
					<th></th>
					<th class="variable">
						<xsl:value-of select="ancestor::item/@id" />
					</th>
				</thead>
			</xsl:if>
			<tbody>
				<xsl:apply-templates select="response" mode="list" />
			</tbody>
		</table>
	</xsl:template>

	<!-- response -->
	<xsl:template
		match="response"
		mode="list">
		<tr>
			<xsl:call-template name="table_tr_even_odd" >
				<xsl:with-param name="position" select="position()"/>
			</xsl:call-template>
			<xsl:choose>
				<xsl:when test="contains(., '[FTE]')">
					<td>
						<xsl:call-template name="table_tr_td" >
							<xsl:with-param name="count" select="count(.)"/>
						</xsl:call-template>
						<xsl:value-of disable-output-escaping="yes" select="substring-before(., '[FTE]')"/>
						<xsl:apply-templates select="following-sibling::responseDescription[1]" mode="list">
							<xsl:with-param name="responsePos">
								<xsl:value-of select="count(preceding-sibling::*)+2" />
							</xsl:with-param>
						</xsl:apply-templates>
					</td>
					<td>
						<xsl:apply-templates select="." mode="form"/>
					</td>
					<td class="td_after_fte">
						<xsl:call-template name="table_tr_td" >
							<xsl:with-param name="count" select="count(.)"/>
						</xsl:call-template>
						<xsl:value-of disable-output-escaping="yes" select="substring-after(., '[FTE]')"/>
					</td>
				</xsl:when>
				<xsl:otherwise>
					<td>
						<xsl:call-template name="table_tr_td" >
							<xsl:with-param name="count" select="count(.)"/>
						</xsl:call-template>
						<xsl:value-of disable-output-escaping="yes" select="."/>
						<xsl:apply-templates select="following-sibling::responseDescription[1]" mode="list">
							<xsl:with-param name="responsePos">
								<xsl:value-of select="count(preceding-sibling::*)+2" />
							</xsl:with-param>
						</xsl:apply-templates>
					</td>
					<td>
						<xsl:apply-templates select="." mode="form"/>
					</td>
					<td class="td_after_fte"></td>
				</xsl:otherwise>
			</xsl:choose>
		</tr>
	</xsl:template>

	<!-- responseDescription -->
	<xsl:template
		match="responseDescription"
		mode="list">
		<xsl:param name="responsePos" />
		<xsl:variable name="descriptionPos" select="count(preceding-sibling::*)+1"/>
		<xsl:if test="$responsePos=$descriptionPos">
			<p>
				<xsl:call-template name="response_description" />
				<xsl:value-of disable-output-escaping="yes" select="."/>
			</p>
		</xsl:if>
	</xsl:template>

	<!-- questionDescription -->
	<xsl:template
		match="questionDescription"
		mode="list">
		<xsl:param name="pos" />
		<p>
			<xsl:call-template name="question_description" />
			<xsl:if test="$pos='top'">
				<xsl:attribute name="class">
					<xsl:text>question_description noMarginTop</xsl:text>
				</xsl:attribute>
			</xsl:if>
			<xsl:value-of select="." disable-output-escaping="yes" />
		</p>
	</xsl:template>
</xsl:stylesheet>