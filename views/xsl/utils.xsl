<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <!--remove the last occurrence of char in a string with all followings characters-->
  <xsl:template name="stripLast">
    <xsl:param name="string" />
    <xsl:param name="char" />
    <xsl:if test="contains($string, $char)">
      <xsl:value-of select="substring-before($string, $char)"/>
      <xsl:call-template name="stripLast">
        <xsl:with-param name="string" select="substring-after($string, $char)"/>
        <xsl:with-param name="char" select="$char"/>
      </xsl:call-template>
    </xsl:if>
  </xsl:template>

</xsl:stylesheet>