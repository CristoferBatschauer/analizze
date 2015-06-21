<?php
/**
 * XOAD HTML DOM Element By Id file.
 *
 * <p>This file defines the {@link XOAD_HTML_DOM_ElementById} Class.</p>
 *
 * @author		Stanimir Angeloff
 *
 * @package		XOAD
 *
 * @subpackage	XOAD_HTML
 *
 * @version		0.6.0.0
 *
 */

/**
 * Loads the file that defines the base class for {@link XOAD_HTML_DOM_ElementById}.
 */
require_once(XOAD_HTML_BASE . '/classes/DOM/BaseElement.class.php');

/**
 * XOAD HTML DOM Element By Id Class.
 *
 * @author		Stanimir Angeloff
 *
 * @package		XOAD
 *
 * @subpackage	XOAD_HTML
 *
 * @version		0.6.0.0
 *
 */
class XOAD_HTML_DOM_ElementById extends XOAD_HTML_DOM_BaseElement
{
	/**
	 * Creates a new instance of the {@link XOAD_HTML_DOM_ElementById} class.
	 *
	 * @param	string	$id		String that holds the ID of the element.
	 *
	 * @access	public
	 *
	 */
	function XOAD_HTML_DOM_ElementById($id)
	{
		parent::XOAD_HTML_DOM_BaseElement();

		$this->id = $id;

		$this->skipKeys[] = 'id';
	}

	/**
	 * Returns the JavaScript name of the element.
	 *
	 * @access	public
	 *
	 * @return	string	The JavaScript name of the element.
	 *
	 */
	function getElement()
	{
		return '__' . ereg_replace('[^a-zA-Z0-9]', '_', $this->id);
	}

	/**
	 * This method removes keyboard focus from the element.
	 *
	 * <p>Example:</p>
	 * <code>
	 * $content =& XOAD_HTML::getElementById('content');
	 *
	 * $content->blur();
	 * </code>
	 *
	 * @access	public
	 *
	 * @return	void
	 *
	 */
	function blur()
	{
		$this->clientCode .= $this->getElement() . '.blur();';
	}

	/**
	 * This method sets focus on the element.
	 *
	 * <p>Example:</p>
	 * <code>
	 * $content =& XOAD_HTML::getElementById('content');
	 *
	 * $content->focus();
	 * </code>
	 *
	 * @access	public
	 *
	 * @return	void
	 *
	 */
	function focus()
	{
		$this->clientCode .= $this->getElement() . '.focus();';
	}

	/**
	 * Returns the JavaScript code of the DOM element.
	 *
	 * <p>You should not call this method directly.</p>
	 *
	 * @access	public
	 *
	 * @param	string	$element	The JavaScript element name.
	 *
	 * @return	string	JavaScript source code for the DOM element.
	 *
	 * @static
	 *
	 */
	function process()
	{
		$element = $this->getElement();

		$returnValue = $element . '=document.getElementById("' . $this->id . '");';

		$returnValue .= parent::process($element);

		return $returnValue;
	}
}
?>