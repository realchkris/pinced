<?php

/**
 * Extracts structured restaurant data from Microdata embedded in the HTML.
 *
 * Searches for schema.org tags using itemprop/itemtype.
 *
 * Responsibilities:
 * - Parse itemtype="Restaurant" blocks
 * - Extract nested address/name info
 *
 * It does NOT:
 * - Parse JSON-LD or infer layout from DOM
 */
