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
		mode="matrix">
		<xsl:if test="position()=1">
			<xsl:choose>
				<xsl:when test="count(questionDescription/following-sibling::question)&gt;0">
					<xsl:apply-templates select="questionDescription" mode="matrix">
						<xsl:with-param name="pos" select="'top'" />
					</xsl:apply-templates>
					<xsl:apply-templates select="question" mode="matrix">
						<xsl:with-param name="pos" select="'bottom'" />
					</xsl:apply-templates>
				</xsl:when>

				<xsl:otherwise>
					<xsl:apply-templates select="question" mode="matrix">
						<xsl:with-param name="pos" select="'top'" />
					</xsl:apply-templates>
					<xsl:apply-templates select="questionDescription" mode="matrix">
						<xsl:with-param name="pos" select="'bottom'" />
					</xsl:apply-templates>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:apply-templates select="instruction" mode="matrix" />
			<xsl:apply-templates select="../item/responses" mode="matrix" />
		</xsl:if>
	</xsl:template>

	<!-- question -->
	<xsl:template
		match="question"
		mode="matrix">
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

	<!-- questionDescription -->
	<xsl:template
		match="questionDescription"
		mode="matrix">
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

	<!-- instruction -->
	<xsl:template
		match="instruction"
		mode="matrix">
		<p>
			<xsl:call-template name="instruction" />
			<xsl:value-of disable-output-escaping="yes" select="."/>
		</p>
	</xsl:template>

	<!-- responses -->
	<xsl:template
		match="responses"
		mode="matrix">
		<xsl:if test="position()=count(../../item/responses)">
			<table class="matrix_table">
				<thead>
					<tr>
						<th></th>
						<xsl:if test="ancestor::itemGroup/@layout!='simpleFieldsList' and ancestor::itemGroup/@layout!='complexFieldsList'">
							<th></th>
						</xsl:if>
						<xsl:apply-templates select="response" mode="matrix_header" />
					</tr>
				</thead>
				<xsl:variable name='footer'>
					<xsl:value-of select="ancestor::itemGroup/footer" />
				</xsl:variable>
				<xsl:if test='$footer!=""'>
					<xsl:apply-templates select="ancestor::itemGroup/footer" mode="layout" />
				</xsl:if>
				<tbody>
					<xsl:variable name='layout'>
						<xsl:value-of select='ancestor::itemGroup/@layout' />
					</xsl:variable>
					<xsl:for-each select="../../item[position()&gt;1]">
						<tr>
							<xsl:call-template name="table_tr_even_odd" >
								<xsl:with-param name="position" select="position()"/>
								<xsl:with-param name="layout" select="$layout"/>
							</xsl:call-template>
							<td>
								<xsl:call-template name="table_tr_td" >
									<xsl:with-param name="count" select="count(responses/response)"/>
								</xsl:call-template>
								<xsl:value-of disable-output-escaping="yes" select="question"/>
							</td>
							<xsl:if test="ancestor::itemGroup/@layout!='simpleFieldsList' and ancestor::itemGroup/@layout!='complexFieldsList'">
								<td class="variable">
									<xsl:value-of select="@id" />
								</td>
							</xsl:if>
							<xsl:apply-templates select="responses/response" mode="matrix_body">
								<xsl:with-param name="row" select="position()"/>
							</xsl:apply-templates>

						</tr>
					</xsl:for-each>
				</tbody>
			</table>
		</xsl:if>
	</xsl:template>

	<!-- response -->
	<xsl:template
		match="response"
		mode="matrix_header">
		<th class="column_header">
			<xsl:choose>
				<xsl:when test="contains(., '[FTE]')">
					<xsl:value-of disable-output-escaping="yes" select="substring-before(., '[FTE]')"/>
					<xsl:value-of disable-output-escaping="yes" select="substring-after(., '[FTE]')"/>
				</xsl:when>
				<xsl:when test="ancestor::itemGroup[@layout='slider']">
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of disable-output-escaping="yes" select="."/>
				</xsl:otherwise>
			</xsl:choose>
		</th>
	</xsl:template>

	<!-- response -->
	<xsl:template
		match="response"
		mode="matrix_body">
		<xsl:param name="row"/>
		<td>
			<xsl:apply-templates select="." mode="form"/>
		</td>
	</xsl:template>

</xsl:stylesheet>