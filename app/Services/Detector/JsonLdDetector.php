<?php

/**
 * Extracts structured restaurant data from JSON-LD script blocks.
 *
 * Looks for schema.org definitions and parses them into structured data.
 *
 * Responsibilities:
 * - Find JSON-LD scripts
 * - Parse and return name/address data
 *
 * It does NOT:
 * - Handle Microdata or DOM-based heuristics
 */
