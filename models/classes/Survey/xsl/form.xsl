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

	<xsl:template
		match="response"
		mode="form_type">
		<xsl:choose>
			<xsl:when test="ancestor::itemGroup[@layout='slider']">
				<xsl:value-of select="'slider'" />
			</xsl:when>
			<xsl:when test="@freeTextEntry='true'">
				<xsl:value-of select="'text'" />
			</xsl:when>
			<xsl:otherwise>
				<xsl:choose>
					<xsl:when test="../@responseCondition='EXACTLY_ONE'">
						<xsl:choose>
							<xsl:when test="count(../response[@freeTextEntry='false'])=1">
								<xsl:value-of select="'checkbox'" />
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="'radio'" />
							</xsl:otherwise>
						</xsl:choose>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="'checkbox'" />
					</xsl:otherwise>
				</xsl:choose>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<!-- response -->
	<xsl:template
		match="response"
		mode="form">
		<xsl:variable name="type">
			<xsl:apply-templates select="." mode="form_type" />
		</xsl:variable>
		<xsl:variable name="code">
			<xsl:apply-templates select="." mode="form_code" />
		</xsl:variable>
		<div class="div_input">
			<xsl:choose>
				<xsl:when test="$type='slider'">
					<div>
						<xsl:attribute name="class">
							<xsl:value-of select="'title'" />
						</xsl:attribute>
						<xsl:value-of select="." />
					</div>
					<div>
						<xsl:attribute name="class">
							<xsl:value-of select="'slider'" />
						</xsl:attribute>
						<xsl:attribute name="id">
							<xsl:value-of select="$code" />
						</xsl:attribute>
						<input>
							<xsl:attribute name="type">
								<xsl:value-of select="'hidden'" />
							</xsl:attribute>
							<xsl:attribute name="id">
								<xsl:value-of select="concat('value_', $code)" />
							</xsl:attribute>
							<xsl:attribute name="value">
								<xsl:text></xsl:text>
							</xsl:attribute>
						</input>
						<input>
							<xsl:attribute name="type">
								<xsl:value-of select="'hidden'" />
							</xsl:attribute>
							<xsl:attribute name="class">
								<xsl:value-of select="'min'" />
							</xsl:attribute>
							<xsl:attribute name="value">
								<xsl:value-of select="../@minValue" />
							</xsl:attribute>
						</input>
						<input>
							<xsl:attribute name="type">
								<xsl:value-of select="'hidden'" />
							</xsl:attribute>
							<xsl:attribute name="class">
								<xsl:value-of select="'max'" />
							</xsl:attribute>
							<xsl:attribute name="value">
								<xsl:value-of select="../@maxValue" />
							</xsl:attribute>
						</input>
						<input>
							<xsl:attribute name="type">
								<xsl:value-of select="'hidden'" />
							</xsl:attribute>
							<xsl:attribute name="class">
								<xsl:value-of select="'step'" />
							</xsl:attribute>
							<xsl:attribute name="value">
								<xsl:value-of select="../@step" />
							</xsl:attribute>
						</input>
					</div>
					<span>
						<xsl:attribute name="class">
							<xsl:value-of select="'levelMin'" />
						</xsl:attribute>
                                                Low
					</span>
					<span>
						<xsl:attribute name="class">
							<xsl:value-of select="'levelMax'" />
						</xsl:attribute>
                                                High
					</span>
				</xsl:when>
				<xsl:when test="$type='text'">
					<input type="text">
						<xsl:attribute name="name">
							<xsl:value-of select="$code" />
						</xsl:attribute>

							<xsl:attribute name="class">
								<xsl:text>variableText</xsl:text>
							</xsl:attribute>
							<xsl:if test="ancestor::itemGroup/@layout!='complexCompleteFields'">
								<xsl:attribute name="value">
									<xsl:choose>
										<xsl:when test="ancestor::itemGroup/@layout='complexFieldsList' and count(ancestor::responses/response)=1">
											<xsl:value-of select="ancestor::item/@id" />
										</xsl:when>
										<xsl:when test="ancestor::itemGroup/@layout='simpleFieldsList'">
											<xsl:value-of select="ancestor::item/@id" />
											<xsl:text>Q</xsl:text>
											<xsl:choose>
												<xsl:when test="string(number(@code))!='NaN'">
													<xsl:if test="number(@code)&lt;10">
														<xsl:text>0</xsl:text>
													</xsl:if>
													<xsl:value-of select="number(@code)" />
												</xsl:when>
												<xsl:otherwise>
													<xsl:value-of select="@code" />
												</xsl:otherwise>
											</xsl:choose>
										</xsl:when>
										<xsl:otherwise>
											<xsl:variable name="suffixVariable">
												<xsl:choose>
													<xsl:when test="string(number(substring(ancestor::item/@id, string-length(ancestor::item/@id)-1)))!='NaN'">
														<xsl:value-of select="substring(ancestor::item/@id, 0, string-length(ancestor::item/@id)-1)" />
														<xsl:if test="count(ancestor::itemGroup/item)&gt;10 or count(ancestor::responses/response)&gt;10">
															<xsl:if test="number(@code)&lt;10">
																<xsl:text>0</xsl:text>
															</xsl:if>
														</xsl:if>
														<xsl:value-of select="number(substring(ancestor::item/@id, string-length(ancestor::item/@id)-1))" />
													</xsl:when>
													<xsl:otherwise>
														<xsl:value-of select="ancestor::item/@id" />
													</xsl:otherwise>
												</xsl:choose>
											</xsl:variable>

											<xsl:variable name="codeResponse">
												
												<xsl:choose>
													<xsl:when test="string(number(@code))!='NaN'">
														<xsl:if test="count(ancestor::itemGroup/item)&gt;10 or count(ancestor::responses/response)&gt;10">
															<xsl:if test="number(@code)&lt;10">
																<xsl:text>0</xsl:text>
															</xsl:if>
														</xsl:if>
														<xsl:value-of select="number(@code)" />
													</xsl:when>
													<xsl:otherwise>
														<xsl:value-of select="@code" />
													</xsl:otherwise>
												</xsl:choose>
												
											</xsl:variable>
											<xsl:value-of select="concat($suffixVariable, $codeResponse)" />
										</xsl:otherwise>
									</xsl:choose>
								</xsl:attribute>
							</xsl:if>

					</input>
				</xsl:when>
				<xsl:when test="$type='radio'">
					<input type="radio">
						<xsl:attribute name="name">
							<xsl:value-of select="$code" />
						</xsl:attribute>
						<xsl:attribute name="value">
							<xsl:value-of select="@code" />
						</xsl:attribute>
					</input>
				</xsl:when>
				<xsl:otherwise>
					<input type="checkbox">
						<xsl:attribute name="name">
							<xsl:value-of select="$code" />
						</xsl:attribute>
						<xsl:attribute name="value">
							<xsl:value-of select="@code" />
						</xsl:attribute>
					</input>
				</xsl:otherwise>
			</xsl:choose>
		</div>
	</xsl:template>

	<!-- response -->
	<xsl:template
		match="response"
		mode="form_code">
		<xsl:variable name="id">
			<xsl:value-of select="../../@id" />
		</xsl:variable>
		<xsl:variable name="code">
			<xsl:value-of select="@code" />
		</xsl:variable>
		<xsl:variable name="type">
			<xsl:apply-templates select="." mode="form_type" />
		</xsl:variable>

		<xsl:if test="$type='text' or $type='checkbox' or $type='slider'">
			<xsl:value-of select="concat($id, '_', $code)" />
		</xsl:if>
		<xsl:if test="$type='radio' or type='textarea'">
			<xsl:value-of select="$id" />
		</xsl:if>
	</xsl:template>

	<!-- response -->
	<xsl:template
		match="response"
		mode="form_get_value">
		<xsl:variable name="type">
			<xsl:apply-templates select="." mode="form_type" />
		</xsl:variable>

		<xsl:if test="$type='text'">
                        $(":text[name='<xsl:apply-templates select="." mode="form_code" />']").val()
		</xsl:if>
		<xsl:if test="$type='radio'">
                        $(":radio[name='<xsl:apply-templates select="." mode="form_code" />']:checked").val()
		</xsl:if>
		<xsl:if test="$type='checkbox'">
                        $(":checkbox[name='<xsl:apply-templates select="." mode="form_code" />']:checked").val()
		</xsl:if>
		<xsl:if test="$type='slider'">
                        get_slider_value('<xsl:apply-templates select="." mode="form_code" />')
		</xsl:if>
	</xsl:template>
	<!-- textarea -->
	<xsl:template
		match="item"
		mode="form_get_value">
			$("textarea[name='
		<xsl:value-of select="@id" />']").val()
	</xsl:template>


</xsl:stylesheet>