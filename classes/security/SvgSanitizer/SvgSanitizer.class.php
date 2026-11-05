<?php
/**
 * SVGSanitizer
 * Whitelist-based PHP SVG sanitizer with XXE protection, DOM parsing fixes,
 * and basic href/style CSS inspection.
 */

class SvgSanitizer {

	// The DOMDocument instance used to parse and manipulate the SVG data.
	private $xmlDoc;

	// A strict whitelist defining allowed SVG elements as keys,
	// and an array of their permitted attributes as values.
	private static $whitelist = array(
		'a' => array('class' => true, 'clip-path' => true, 'clip-rule' => true, 'fill' => true, 'fill-opacity' => true, 'fill-rule' => true, 'filter' => true, 'id' => true, 'mask' => true, 'opacity' => true, 'stroke' => true, 'stroke-dasharray' => true, 'stroke-dashoffset' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true, 'stroke-miterlimit' => true, 'stroke-opacity' => true, 'stroke-width' => true, 'style' => true, 'systemLanguage' => true, 'transform' => true, 'href' => true, 'xlink:href' => true, 'xlink:title' => true),
		'circle' => array('class' => true, 'clip-path' => true, 'clip-rule' => true, 'cx' => true, 'cy' => true, 'fill' => true, 'fill-opacity' => true, 'fill-rule' => true, 'filter' => true, 'id' => true, 'mask' => true, 'opacity' => true, 'r' => true, 'requiredFeatures' => true, 'stroke' => true, 'stroke-dasharray' => true, 'stroke-dashoffset' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true, 'stroke-miterlimit' => true, 'stroke-opacity' => true, 'stroke-width' => true, 'style' => true, 'systemLanguage' => true, 'transform' => true),
		'clipPath' => array('class' => true, 'clipPathUnits' => true, 'id' => true),
		'defs' => array('id' => true, 'class' => true),
		'style' => array('type' => true),
		'desc' => array(),
		'ellipse' => array('class' => true, 'clip-path' => true, 'clip-rule' => true, 'cx' => true, 'cy' => true, 'fill' => true, 'fill-opacity' => true, 'fill-rule' => true, 'filter' => true, 'id' => true, 'mask' => true, 'opacity' => true, 'requiredFeatures' => true, 'rx' => true, 'ry' => true, 'stroke' => true, 'stroke-dasharray' => true, 'stroke-dashoffset' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true, 'stroke-miterlimit' => true, 'stroke-opacity' => true, 'stroke-width' => true, 'style' => true, 'systemLanguage' => true, 'transform' => true),
		'feGaussianBlur' => array('class' => true, 'color-interpolation-filters' => true, 'id' => true, 'requiredFeatures' => true, 'stdDeviation' => true),
		'filter' => array('class' => true, 'color-interpolation-filters' => true, 'filterRes' => true, 'filterUnits' => true, 'height' => true, 'id' => true, 'primitiveUnits' => true, 'requiredFeatures' => true, 'width' => true, 'x' => true, 'y' => true, 'href' => true, 'xlink:href' => true),
		'foreignObject' => array('class' => true, 'font-size' => true, 'height' => true, 'id' => true, 'opacity' => true, 'requiredFeatures' => true, 'style' => true, 'transform' => true, 'width' => true, 'x' => true, 'y' => true),
		'g' => array('class' => true, 'clip-path' => true, 'clip-rule' => true, 'id' => true, 'display' => true, 'fill' => true, 'fill-opacity' => true, 'fill-rule' => true, 'filter' => true, 'mask' => true, 'opacity' => true, 'requiredFeatures' => true, 'stroke' => true, 'stroke-dasharray' => true, 'stroke-dashoffset' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true, 'stroke-miterlimit' => true, 'stroke-opacity' => true, 'stroke-width' => true, 'style' => true, 'systemLanguage' => true, 'transform' => true, 'font-family' => true, 'font-size' => true, 'font-style' => true, 'font-weight' => true, 'text-anchor' => true),
		'image' => array('class' => true, 'clip-path' => true, 'clip-rule' => true, 'filter' => true, 'height' => true, 'id' => true, 'mask' => true, 'opacity' => true, 'requiredFeatures' => true, 'style' => true, 'systemLanguage' => true, 'transform' => true, 'width' => true, 'x' => true, 'y' => true, 'href' => true, 'xlink:href' => true, 'xlink:title' => true),
		'line' => array('class' => true, 'clip-path' => true, 'clip-rule' => true, 'fill' => true, 'fill-opacity' => true, 'fill-rule' => true, 'filter' => true, 'id' => true, 'marker-end' => true, 'marker-mid' => true, 'marker-start' => true, 'mask' => true, 'opacity' => true, 'requiredFeatures' => true, 'stroke' => true, 'stroke-dasharray' => true, 'stroke-dashoffset' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true, 'stroke-miterlimit' => true, 'stroke-opacity' => true, 'stroke-width' => true, 'style' => true, 'systemLanguage' => true, 'transform' => true, 'x1' => true, 'x2' => true, 'y1' => true, 'y2' => true),
		'linearGradient' => array('class' => true, 'id' => true, 'gradientTransform' => true, 'gradientUnits' => true, 'requiredFeatures' => true, 'spreadMethod' => true, 'systemLanguage' => true, 'x1' => true, 'x2' => true, 'y1' => true, 'y2' => true, 'href' => true, 'xlink:href' => true),
		'marker' => array('id' => true, 'class' => true, 'markerHeight' => true, 'markerUnits' => true, 'markerWidth' => true, 'orient' => true, 'preserveAspectRatio' => true, 'refX' => true, 'refY' => true, 'systemLanguage' => true, 'viewBox' => true),
		'mask' => array('class' => true, 'height' => true, 'id' => true, 'maskContentUnits' => true, 'maskUnits' => true, 'width' => true, 'x' => true, 'y' => true),
		'metadata' => array('class' => true, 'id' => true),
		'path' => array('class' => true, 'clip-path' => true, 'clip-rule' => true, 'd' => true, 'fill' => true, 'fill-opacity' => true, 'fill-rule' => true, 'filter' => true, 'id' => true, 'marker-end' => true, 'marker-mid' => true, 'marker-start' => true, 'mask' => true, 'opacity' => true, 'requiredFeatures' => true, 'stroke' => true, 'stroke-dasharray' => true, 'stroke-dashoffset' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true, 'stroke-miterlimit' => true, 'stroke-opacity' => true, 'stroke-width' => true, 'style' => true, 'systemLanguage' => true, 'transform' => true),
		'pattern' => array('class' => true, 'height' => true, 'id' => true, 'patternContentUnits' => true, 'patternTransform' => true, 'patternUnits' => true, 'requiredFeatures' => true, 'style' => true, 'systemLanguage' => true, 'viewBox' => true, 'width' => true, 'x' => true, 'y' => true, 'href' => true, 'xlink:href' => true),
		'polygon' => array('class' => true, 'clip-path' => true, 'clip-rule' => true, 'id' => true, 'fill' => true, 'fill-opacity' => true, 'fill-rule' => true, 'filter' => true, 'marker-end' => true, 'marker-mid' => true, 'marker-start' => true, 'mask' => true, 'opacity' => true, 'points' => true, 'requiredFeatures' => true, 'stroke' => true, 'stroke-dasharray' => true, 'stroke-dashoffset' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true, 'stroke-miterlimit' => true, 'stroke-opacity' => true, 'stroke-width' => true, 'style' => true, 'systemLanguage' => true, 'transform' => true),
		'polyline' => array('class' => true, 'clip-path' => true, 'clip-rule' => true, 'id' => true, 'fill' => true, 'fill-opacity' => true, 'fill-rule' => true, 'filter' => true, 'marker-end' => true, 'marker-mid' => true, 'marker-start' => true, 'mask' => true, 'opacity' => true, 'points' => true, 'requiredFeatures' => true, 'stroke' => true, 'stroke-dasharray' => true, 'stroke-dashoffset' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true, 'stroke-miterlimit' => true, 'stroke-opacity' => true, 'stroke-width' => true, 'style' => true, 'systemLanguage' => true, 'transform' => true),
		'radialGradient' => array('class' => true, 'cx' => true, 'cy' => true, 'fx' => true, 'fy' => true, 'gradientTransform' => true, 'gradientUnits' => true, 'id' => true, 'r' => true, 'requiredFeatures' => true, 'spreadMethod' => true, 'systemLanguage' => true, 'href' => true, 'xlink:href' => true),
		'rect' => array('class' => true, 'clip-path' => true, 'clip-rule' => true, 'fill' => true, 'fill-opacity' => true, 'fill-rule' => true, 'filter' => true, 'height' => true, 'id' => true, 'mask' => true, 'opacity' => true, 'requiredFeatures' => true, 'rx' => true, 'ry' => true, 'stroke' => true, 'stroke-dasharray' => true, 'stroke-dashoffset' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true, 'stroke-miterlimit' => true, 'stroke-opacity' => true, 'stroke-width' => true, 'style' => true, 'systemLanguage' => true, 'transform' => true, 'width' => true, 'x' => true, 'y' => true),
		'stop' => array('class' => true, 'id' => true, 'offset' => true, 'requiredFeatures' => true, 'stop-color' => true, 'stop-opacity' => true, 'style' => true, 'systemLanguage' => true),
		'svg' => array('class' => true, 'clip-path' => true, 'clip-rule' => true, 'filter' => true, 'id' => true, 'height' => true, 'mask' => true, 'preserveAspectRatio' => true, 'requiredFeatures' => true, 'style' => true, 'systemLanguage' => true, 'viewBox' => true, 'width' => true, 'x' => true, 'xmlns' => true, 'xmlns:se' => true, 'xmlns:xlink' => true, 'y' => true),
		'switch' => array('class' => true, 'id' => true, 'requiredFeatures' => true, 'systemLanguage' => true),
		'symbol' => array('class' => true, 'fill' => true, 'fill-opacity' => true, 'fill-rule' => true, 'filter' => true, 'font-family' => true, 'font-size' => true, 'font-style' => true, 'font-weight' => true, 'id' => true, 'opacity' => true, 'preserveAspectRatio' => true, 'requiredFeatures' => true, 'stroke' => true, 'stroke-dasharray' => true, 'stroke-dashoffset' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true, 'stroke-miterlimit' => true, 'stroke-opacity' => true, 'stroke-width' => true, 'style' => true, 'systemLanguage' => true, 'transform' => true, 'viewBox' => true),
		'text' => array('class' => true, 'clip-path' => true, 'clip-rule' => true, 'fill' => true, 'fill-opacity' => true, 'fill-rule' => true, 'filter' => true, 'font-family' => true, 'font-size' => true, 'font-style' => true, 'font-weight' => true, 'id' => true, 'mask' => true, 'opacity' => true, 'requiredFeatures' => true, 'stroke' => true, 'stroke-dasharray' => true, 'stroke-dashoffset' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true, 'stroke-miterlimit' => true, 'stroke-opacity' => true, 'stroke-width' => true, 'style' => true, 'systemLanguage' => true, 'text-anchor' => true, 'transform' => true, 'x' => true, 'xml:space' => true, 'y' => true),
		'textPath' => array('class' => true, 'id' => true, 'method' => true, 'requiredFeatures' => true, 'spacing' => true, 'startOffset' => true, 'style' => true, 'systemLanguage' => true, 'transform' => true, 'href' => true, 'xlink:href' => true),
		'title' => array(),
		'tspan' => array('class' => true, 'clip-path' => true, 'clip-rule' => true, 'dx' => true, 'dy' => true, 'fill' => true, 'fill-opacity' => true, 'fill-rule' => true, 'filter' => true, 'font-family' => true, 'font-size' => true, 'font-style' => true, 'font-weight' => true, 'id' => true, 'mask' => true, 'opacity' => true, 'requiredFeatures' => true, 'rotate' => true, 'stroke' => true, 'stroke-dasharray' => true, 'stroke-dashoffset' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true, 'stroke-miterlimit' => true, 'stroke-opacity' => true, 'stroke-width' => true, 'style' => true, 'systemLanguage' => true, 'text-anchor' => true, 'textLength' => true, 'transform' => true, 'x' => true, 'xml:space' => true, 'y' => true),
		'use' => array('class' => true, 'clip-path' => true, 'clip-rule' => true, 'fill' => true, 'fill-opacity' => true, 'fill-rule' => true, 'filter' => true, 'height' => true, 'id' => true, 'mask' => true, 'stroke' => true, 'stroke-dasharray' => true, 'stroke-dashoffset' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true, 'stroke-miterlimit' => true, 'stroke-opacity' => true, 'stroke-width' => true, 'style' => true, 'transform' => true, 'width' => true, 'x' => true, 'y' => true, 'href' => true, 'xlink:href' => true),
	);

	function __construct() {
		$this->xmlDoc = new DOMDocument();
		$this->xmlDoc->preserveWhiteSpace = false; // Prevents the parser from creating unnecessary text nodes for whitespace
	}

	function load($file) {
		$content = file_get_contents($file);
		if (!is_string($content) || $content === '' || preg_match('/<!DOCTYPE/i', $content)) {
			return false;
		}
		$previousErrors = libxml_use_internal_errors(true);
		$this->xmlDoc->resolveExternals = false;
		$this->xmlDoc->substituteEntities = false;
		if (PHP_VERSION_ID < 80000) {
			$previousLoader = libxml_disable_entity_loader(true);
		}
		// Parse bytes so disabling external entities does not block the local file itself.
		$loaded = $this->xmlDoc->loadXML($content, LIBXML_NONET);
		if (PHP_VERSION_ID < 80000) {
			libxml_disable_entity_loader($previousLoader);
		}
		libxml_clear_errors();
		libxml_use_internal_errors($previousErrors);
		return $loaded;
	}

	function sanitize() {
		// Retrieve all elements within the SVG document.
		// Note: getElementsByTagName returns a "live" DOMNodeList. Modifying the DOM
		// during iteration causes indexes to shift, leading to skipped elements.
		$allElements = $this->xmlDoc->getElementsByTagName("*");

		// Array to safely store nodes that need to be deleted after the iteration finishes.
		$nodesToRemove = array();

		foreach ($allElements as $currentNode) {

			// 1. Tag Validation: If the element's tag is not in the whitelist, mark it for removal.
			if (!isset(self::$whitelist[$currentNode->tagName])) {
				$nodesToRemove[] = $currentNode;
				continue;
			}

			$attributesWhitelist = self::$whitelist[$currentNode->tagName];
			$attributesToRemove = array();

			// 2. Attribute Validation: Iterate through all attributes of the current, allowed element.
			for ($j = 0; $j < $currentNode->attributes->length; $j++) {
				$attr = $currentNode->attributes->item($j);
				$attrName = strtolower($attr->name);
				$attrValue = $attr->value;

				// 2a. If the attribute is not explicitly allowed for this specific tag, mark it for removal.
				if (!isset($attributesWhitelist[$attr->name])) {
					$attributesToRemove[] = $attr->name;
					continue;
				}

				// 2b. XSS Protection for Links/References:
				// Prevent dangerous URI schemes like javascript:, vbscript:, or data: in href attributes.
				if ($attrName === 'href' || $attrName === 'xlink:href') {
					if (preg_match('/^\s*(javascript|vbscript|data):/i', $attrValue)) {
						$attributesToRemove[] = $attr->name;
					}
				}

				// 2c. XSS Protection for Inline Styles:
				// Prevent malicious CSS injections (e.g., executing javascript, CSS expressions, or calling external URLs).
				if ($attrName === 'style') {
					if (preg_match('/(?:javascript|expression|behavior|url\s*\()/i', $attrValue)) {
						$attributesToRemove[] = $attr->name;
					}
				}
			}

			// Actually remove the blocked attributes from the current element.
			foreach ($attributesToRemove as $attrName) {
				$currentNode->removeAttribute($attrName);
			}

			// 3. Special Case for <style> Tags:
			// Inspect the inner content of <style> elements for malicious CSS payloads.
			if ($currentNode->tagName === 'style') {
				if (preg_match('/(?:javascript|expression|behavior|url\s*\()/i', $currentNode->textContent)) {
					$nodesToRemove[] = $currentNode;
				}
			}
		}

		// 4. Final Cleanup: Safely remove all marked nodes from the DOM tree.
		// This approach prevents the DOMNodeList index-shifting bug mentioned earlier.
		foreach ($nodesToRemove as $node) {
			if ($node->parentNode) {
				$node->parentNode->removeChild($node);
			}
		}
	}

	// Returns the sanitized SVG as an XML string.
	function saveSVG() {
		$this->xmlDoc->formatOutput = true;
		return $this->xmlDoc->saveXML();
	}

	// Saves the sanitized SVG back to a file.
	function save($file) {
		$this->xmlDoc->formatOutput = true;
		return $this->xmlDoc->save($file);
	}
}
