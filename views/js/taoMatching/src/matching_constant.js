/**
 * @namespace TAO_MATCHING
 * @description TAO_MATCHING contains all the features required to score an item
 */
TAO_MATCHING = typeof TAO_MATCHING != 'undefined' ? TAO_MATCHING : {};
/**
 * @namespace TAO_MATCHING.RULE
 * @description Available predefined rules
 * @memberOf TAO_MATCHING
 */
TAO_MATCHING.RULE = typeof TAO_MATCHING.RULE != 'undefined' ? TAO_MATCHING.RULE : {};
/**
 * @field
 * @description Predefined match correct rule
 * @type string
 * @static
 */
TAO_MATCHING.RULE.MATCH_CORRECT = "if(match(null, getResponse('RESPONSE'), getCorrect('RESPONSE'))) setOutcomeValue('SCORE', 1); else setOutcomeValue('SCORE', 0);"; 
/**
 * @field
 * @description Predefined map response rule
 * @type string
 * @ 
 */
TAO_MATCHING.RULE.MAP_RESPONSE = "if(isNull(null, getResponse('RESPONSE'))) { setOutcomeValue('SCORE', 0); } else { setOutcomeValue('SCORE', mapResponse(null, getMap('RESPONSE'), getResponse('RESPONSE'))); }";
