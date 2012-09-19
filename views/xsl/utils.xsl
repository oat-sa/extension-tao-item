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
  <!--get the style attribute to have the width of column-->
  <xsl:template name="columnWidth">
    <xsl:param name="match" />
    <xsl:if test="ancestor::itemGroup/styles[@type='column'][@match=$match]/key[@name='size']">
      <xsl:attribute name="style">width:<xsl:value-of select="ancestor::itemGroup/styles[@type='column'][@match=$match]/key[@name='size']" />%;</xsl:attribute>
    </xsl:if>
  </xsl:template>
</xsl:stylesheet>