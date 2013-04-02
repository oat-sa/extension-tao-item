/*  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */
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
