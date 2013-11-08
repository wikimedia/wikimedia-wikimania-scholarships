<?php
/**
 * @section LICENSE
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 */

namespace Wikimania\Scholarship;

/**
 * @author Bryan Davis <bd808@wikimedia.org>
 * @copyright © 2013 Bryan Davis and Wikimedia Foundation.
 */
class FormTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @covers Form
	 */
	public function testRequired () {
		$form = new Form();
		$form->expectString( 'foo', array( 'required' => true ) );

		$this->assertFalse( $form->validate(), 'Form should be invalid' );
		$vals = $form->getValues();
		$this->assertArrayHasKey( 'foo', $vals );
		$this->assertNull( $vals['foo'] );
		$this->assertContains( 'foo', $form->getErrors() );
	}

	/**
	 * @covers Form
	 */
	public function testDefaultWhenEmpty () {
		$form = new Form();
		$form->expectString( 'foo', array( 'default' => 'bar' ) );

		$this->assertTrue( $form->validate(), 'Form should be valid' );
		$vals = $form->getValues();
		$this->assertArrayHasKey( 'foo', $vals );
		$this->assertEquals( 'bar', $vals['foo'] );
		$this->assertNotContains( 'foo', $form->getErrors() );
	}

	/**
	 * @covers Form
	 */
	public function testNotInArray () {
		$form = new Form();
		$form->expectInArray( 'foo', array( 'bar' ) );

		$this->assertFalse( $form->validate(), 'Form should be invalid' );
		$vals = $form->getValues();
		$this->assertArrayHasKey( 'foo', $vals );
		$this->assertNull( $vals['foo'] );
		$this->assertContains( 'foo', $form->getErrors() );
	}

	/**
	 * @covers Form
	 */
	public function testInArray () {
		$_POST['foo'] = 'bar';
		$form = new Form();
		$form->expectInArray( 'foo', array( 'bar' ) );

		$this->assertTrue( $form->validate(), 'Form should be valid' );
		$vals = $form->getValues();
		$this->assertArrayHasKey( 'foo', $vals );
		$this->assertEquals( 'bar', $vals['foo'] );
		$this->assertNotContains( 'foo', $form->getErrors() );
	}

	/**
	 * @covers Form
	 */
	public function testEncodeBasic () {
		$input = array(
			'foo' => 1,
			'bar' => 'this=that',
			'baz' => 'tom & jerry',
		);
		$output = Form::urlEncode( $input );
		$this->assertEquals( 'foo=1&bar=this%3Dthat&baz=tom+%26+jerry', $output );
	}

	/**
	 * @covers Form
	 */
	public function testEncodeArray () {
		$input = array(
			'foo' => array( 'a', 'b', 'c' ),
			'bar[]' => array( 1, 2, 3 ),
		);
		$output = Form::urlEncode( $input );
		$this->assertEquals(
			'foo=a&foo=b&foo=c&bar%5B%5D=1&bar%5B%5D=2&bar%5B%5D=3', $output );
	}

	/**
	 * @covers Form
	 */
	public function testQsMerge () {
		$_GET['foo'] = 1;
		$_GET['bar'] = 'this=that';
		$_GET['baz'] = 'tom & jerry';

		$output = Form::qsMerge();
		$this->assertEquals( 'foo=1&bar=this%3Dthat&baz=tom+%26+jerry', $output );

		$output = Form::qsMerge( array( 'foo' => 2, 'xyzzy' => 'grue' ) );
		$this->assertEquals( 'foo=2&bar=this%3Dthat&baz=tom+%26+jerry&xyzzy=grue', $output );
	}

	/**
	 * @covers Form
	 */
	public function testQsRemove () {
		$_GET['foo'] = 1;
		$_GET['bar'] = 'this=that';
		$_GET['baz'] = 'tom & jerry';

		$output = Form::qsRemove();
		$this->assertEquals( 'foo=1&bar=this%3Dthat&baz=tom+%26+jerry', $output );

		$output = Form::qsRemove( array( 'bar' ) );
		$this->assertEquals( 'foo=1&baz=tom+%26+jerry', $output );
	}

} //end FormTest
