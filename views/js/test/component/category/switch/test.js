/**
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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA ;
 */

/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery',
    'taoItems/component/category/switch'
], function($, categorySwitchComponent) {
    'use strict';

    var cardComponentApi = [
        { name : 'init', title : 'init' },
        { name : 'destroy', title : 'destroy' },
        { name : 'render', title : 'render' },
        { name : 'show', title : 'show' },
        { name : 'hide', title : 'hide' },
        { name : 'enable', title : 'enable' },
        { name : 'disable', title : 'disable' },
        { name : 'is', title : 'is' },
        { name : 'setState', title : 'setState' },
        { name : 'getContainer', title : 'getContainer' },
        { name : 'getElement', title : 'getElement' },
        { name : 'getTemplate', title : 'getTemplate' },
        { name : 'setTemplate', title : 'setTemplate' }
    ];


    QUnit.module('API');

    QUnit.test('module', function(assert) {
        QUnit.expect(3);

        assert.equal(typeof categorySwitchComponent, 'function', "The cardComponent module exposes a function");
        assert.equal(typeof categorySwitchComponent(null, 'foo'), 'object', "The cardComponent factory produces an object");
        assert.notStrictEqual(categorySwitchComponent(null, 'foo'), categorySwitchComponent(null, 'foo'), "The cardComponent factory provides a different object on each call");
    });

    QUnit
        .cases(cardComponentApi)
        .test('instance API ', function(data, assert) {
            var instance = categorySwitchComponent(null, 'foo');
            assert.equal(typeof instance[data.name], 'function', 'The cardComponent instance exposes a "' + data.title + '" function');
        });

    QUnit.test('Missing parameters', function(assert){
        var $container = $('#qunit-fixture');

        QUnit.expect(2);

        assert.throws(categorySwitchComponent, TypeError, 'PropId must be defined');
        assert.throws(function() { categorySwitchComponent($container); }, TypeError, 'PropId must be defined');
    });


    QUnit.module('Behavior');

    QUnit.asyncTest('DOM rendering', function(assert) {
        var $container = $('#qunit-fixture');

        QUnit.expect(5);

        categorySwitchComponent( $container, 'foo', true )
            .on('render', function(){

                assert.equal($('.category-auto-adder', $container).length, 1, 'The container has the component root element');
                assert.ok($('.category-auto-adder', $container).hasClass('rendered'), 'The component root element has the rendered class');
                assert.ok($('.category-auto-adder', $container).hasClass('txt-success'), 'The component root element has the txt-success class');
                assert.equal($('.category-auto-adder', $container).data('id'), 'foo', 'The id is correct element');
                assert.deepEqual($('.category-auto-adder', $container)[0], this.getElement()[0], 'The component element is correct');

                QUnit.start();
            });
    });

    QUnit.asyncTest('request change', function(assert) {
        var $container = $('#qunit-fixture');

        QUnit.expect(3);

        categorySwitchComponent( $container, 'foo', true )
            .on('render', function(){
                var $element = this.getElement();
                assert.equal($element.length, 1, 'The container has the component root element');

                $element.trigger('click');
            })
            .on('requestChange', function(propUri, value){

                assert.equal(propUri, 'foo', 'The property URI is correct');
                assert.equal(value, false, 'The exposed value is correct');
                QUnit.start();
            });
    });


    QUnit.asyncTest('update value', function(assert) {
        var $container = $('#qunit-fixture');

        QUnit.expect(7);

        categorySwitchComponent( $container, 'foo', true )
            .on('render', function(){
                var $element = this.getElement();
                assert.equal($element.length, 1, 'The container has the component root element');
                assert.ok($('.category-auto-adder', $container).hasClass('txt-success'), 'The component root element has the txt-success class');

                assert.ok(this.isExposed(), 'The prop is initialized exposed');

                $element.trigger('click');

            })
            .on('requestChange', function(propUri, value){

                assert.equal(propUri, 'foo', 'The property URI is correct');
                assert.equal(value, false, 'The exposed value is correct');

                this.setExposed(value);

                assert.ok(!$('.category-auto-adder', $container).hasClass('txt-success'), 'The component root element has not the txt-success class');
                assert.ok(!this.isExposed(), 'The prop is not exposed anymore');

                QUnit.start();
            });
    });

});
