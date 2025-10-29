<?php

namespace mod_assessment\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class SwpdpExtension extends AbstractExtension
{
	protected $container;
	
	public function __constructXxx(ContainerInterface $container)
	{
		$this->container = $container;
	}
	
	public function getFilters()
	{
		return array(
			new TwigFilter('labelSplit', array($this, 'labelSplitFilter')),
			new TwigFilter('inputSplit', array($this, 'inputSplitFilter')),
			new TwigFilter('displayLabel', array($this, 'displayLabelFilter')),
			new TwigFilter('displayLabelInline', array($this, 'displayLabelInlineFilter')),
			new TwigFilter('dynamicTable', array($this, 'dynamicTableFilter')),
			new TwigFilter('classShort', array($this, 'classShort')),
		);
	}

	public function labelSplitFilter($attributes)
	{
		// Default values
		$labelSplit = 3;
		$inputSplit = 9;
		
		if (isset($attributes['split']) && $attributes['split'])
		{
			$splits = explode('|', $attributes['split']);
			if (count($splits) === 2)
			{
				$labelSplit = $splits[0];
				$inputSplit = $splits[1];
			}
		}
		if (isset($attributes['displayLabel']) && $attributes['displayLabel'] === false)
		{
			$labelSplit = 0;
			$inputSplit = 12;
		}

		// Return
		return $labelSplit;
	}

	public function inputSplitFilter($attributes)
	{
		// Default values
		$labelSplit = 3;
		$inputSplit = 9;

		if (isset($attributes['split']) && $attributes['split'])
		{
			$splits = explode('|', $attributes['split']);
			if (count($splits) === 2)
			{
				$labelSplit = $splits[0];
				$inputSplit = $splits[1];
			}
		}
		if (isset($attributes['displayLabel']) && $attributes['displayLabel'] === false)
		{
			$labelSplit = 0;
			$inputSplit = 12;
		}
		
		// Return
		return $inputSplit;
	}
	
	public function displayLabelFilter($attributes)
	{
		// Default value
		$displayLabel = true;
		
		if (isset($attributes['displayLabel']) && $attributes['displayLabel'] === false)
		{
			$displayLabel = false;
		}
		
		// Return
		return $displayLabel;
	}
	
	public function displayLabelInlineFilter($attributes)
	{
		// Default value
		$displayLabelInline = false;
		
		if (isset($attributes['displayLabelInline']) && $attributes['displayLabelInline'] === true)
		{
			$displayLabelInline = true;
		}
		
		// Return
		return $displayLabelInline;
	}
	
	public function dynamicTableFilter($html)
	{
		// Get the required handles
		$request = $this->container->get('request');
		$user = $this->container->get('security.token_storage')->getToken()->getUser();

		// Retrieve the route from the request
		$route = $request->get('_route');
		
		// Load the HTML into a DOMDocument
		$doc = new \DOMDocument();
		// Use a try/catch block in case the HTML is not valid
		try 
		{
			// Rather than throw parse errors, store them internally
			libxml_use_internal_errors(true);
			$doc->loadHTML($html);
			if (count(libxml_get_errors()) > 0)
			{
				// If the HTML is not valid, return the HTML unchanged
				echo $html;
				return;
			}
			$xpath = new \DOMXPath($doc);
		}
		catch (\Exception $e)
		{
			// If the HTML is not valid, return the HTML unchanged
			echo $html;
			return;
		}
		
		// Get a handle on the <table>
		$tableElements = $doc->getElementsByTagName('table');
		$tableElement = $tableElements->item(0);
		
		// Work out whether this table is dynamic
		$isDynamicTable = $tableElement->getAttribute('data-1d-is-dynamic-table');
		
		// If this table is not dynamic, return the HTML unchanged
		if (!$isDynamicTable)
		{
			echo $html;
			return;
		}
		
		// Go to the table <thead> to get the columns
		$theadElement = $tableElement->getElementsByTagName('thead')->item(0);
		$theadTrElement = $theadElement->getElementsByTagName('tr')->item(0);
		$theadTrThElements = $theadTrElement->getElementsByTagName('th');

		// For safety, check that every <th> element has the data-1d-dynamic-table-column-name element set.
		// If not, we can't do the ordering properly, so return the HTML unchanged
		foreach ($theadTrThElements as $key => $thElement)
		{
			if (!$thElement->getAttribute('data-1d-dynamic-table-column-name'))
			{
				echo $html;
				return;
			}
		}
		
		// Before reordering the columns, add a button to the last column (which should always be fixed).
		// This button will provide the means to reorder the columns.
		$url = $this->container->get('router')->generate('core_reorder_dynamic_table_columns', array('route' => $route));
		$html = '<a class="btn btn-default btn-xs tooltips pull-right" id="reorder-dynamic-table-columns-button" data-original-title="Reorder columns" data-toggle="modal" data-target="#ajax" href="' . $url . '"><i class="fa fa-exchange"></i></a>';
		$fragment = $doc->createDocumentFragment();
		$fragment->appendXML($html);
		$theadTrThElements->item($theadTrThElements->length - 1)->appendChild($fragment);

		// Build arrays of the dynamic/fixed column names and titles
		$columnKeysIndexedOnName = array();
		$columnNamesFixedFirst = array();
		$columnNamesDynamic = array();
		$columnNamesFixedLast = array();
		$columnnNamesOrdered = array();
		$columnTitlesIndexedOnName = array();
		foreach ($theadTrThElements as $key => $thElement)
		{
			$columnKeysIndexedOnName[$thElement->getAttribute('data-1d-dynamic-table-column-name')] = $key;
			if (strtolower($thElement->getAttribute('data-1d-dynamic-table-fixed')) === 'first')
			{
				$columnNamesFixedFirst[] = $thElement->getAttribute('data-1d-dynamic-table-column-name');
			}
			else if (strtolower($thElement->getAttribute('data-1d-dynamic-table-fixed')) === 'last')
			{
				$columnNamesFixedLast[] = $thElement->getAttribute('data-1d-dynamic-table-column-name');
			}
			else
			{
				$columnNamesDynamic[] = $thElement->getAttribute('data-1d-dynamic-table-column-name');
				if (strtolower($thElement->getAttribute('data-1d-dynamic-table-show')) === 'true')
				{
					// This column will be displayed by default
					$columnnNamesOrdered[] = $thElement->getAttribute('data-1d-dynamic-table-column-name');
				}
			}
			$columnTitlesIndexedOnName[$thElement->getAttribute('data-1d-dynamic-table-column-name')] = trim($thElement->nodeValue);
		}
		
		// Retrieve the User table setting, if it exists for this route
		if ($user->getSetting('dynamic_table_column_names_ordered_' . $route))
		{
			$setting = $user->getSetting('dynamic_table_column_names_ordered_' . $route);
			$columnNamesOrderedDirty = explode(',', $setting);
			// Clean $columnNamesOrderedDirty by removing any elements that are not in $columnNamesDynamic
			$columnNamesOrderedClean = array();
			foreach ($columnNamesOrderedDirty as $columnName)
			{
				if (in_array($columnName, $columnNamesDynamic))
				{
					$columnNamesOrderedClean[] = $columnName;
				}
			}
			// If there any valid column names, use this user-specific setting for the column order
			if (count($columnNamesOrderedClean) > 0)
			{
				$columnnNamesOrdered = $columnNamesOrderedClean;
			}
		}
		
		// Iterate each <tr>
		$trElements = $tableElement->getElementsByTagName('tr');
		foreach ($trElements as $trElement)
		{
			// If the <tr> contains any <th> or <td> elements, order them according to $showCols
			$thOrTdElements = $xpath->query('th|td', $trElement);
			if ($thOrTdElements->length > 0)
			{
				$trElementOrdered = $doc->createElement('tr');
				foreach ($columnNamesFixedFirst as $columnName)
				{
					$thOrTdElement = $thOrTdElements->item($columnKeysIndexedOnName[$columnName]);
					if ($thOrTdElement)
					{
						$trElementOrdered->appendChild($thOrTdElement->cloneNode(true));
					}
				}
				foreach ($columnnNamesOrdered as $columnName)
				{
					$thOrTdElement = $thOrTdElements->item($columnKeysIndexedOnName[$columnName]);
					if ($thOrTdElement)
					{
						$trElementOrdered->appendChild($thOrTdElement->cloneNode(true));
					}
				}
				foreach ($columnNamesFixedLast as $columnName)
				{
					$thOrTdElement = $thOrTdElements->item($columnKeysIndexedOnName[$columnName]);
					if ($thOrTdElement)
					{
						$trElementOrdered->appendChild($thOrTdElement->cloneNode(true));
					}
				}
				$trElement->parentNode->replaceChild($trElementOrdered, $trElement);
			}
		}

		// Create the HTML output
		$finalHtml = '';
		$bodyTag = $doc->documentElement->getElementsByTagName('body')->item(0);
		foreach ($bodyTag->childNodes as $rootLevelTag)
		{
			$finalHtml .= $doc->saveHTML($rootLevelTag);
		}
		
		// Append a <div> containing $columnNamesDynamic so that the client-side JavaScript
		// can render a 'reorder columns' feature 
		$divHtml = '<div id="1d-dynamic-table-data-div"';
		$divHtml .= ' data-1d-dynamic-table-column-names-fixed-first="' . join(',', $columnNamesFixedFirst) . '"';
		$divHtml .= ' data-1d-dynamic-table-column-names-dynamic="' . join(',', $columnNamesDynamic) . '"';
		$divHtml .= ' data-1d-dynamic-table-column-names-fixed-last="' . join(',', $columnNamesFixedLast) . '"';
		$divHtml .= ' data-1d-dynamic-table-column-titles="' . htmlspecialchars(json_encode($columnTitlesIndexedOnName)) . '"';
		$divHtml .= '></div>';
		
		$finalHtml .= $divHtml;
				
		echo $finalHtml;
	}
	
	public function getName()
	{
		return 'swpdp_extension';
	}

	public function classShort($object)
	{
		$reflect = new \ReflectionClass($object);
	
		return $reflect->getShortName();
	}
}