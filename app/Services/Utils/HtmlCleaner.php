<?php

/**
 * Cleans and normalizes raw HTML before parsing.
 *
 * Strips scripts, fixes malformed tags, and prepares HTML for DOMCrawler.
 *
 * Responsibilities:
 * - Preprocess raw HTML
 * - Improve parser reliability
 *
 * It does NOT:
 * - Modify or interpret page content
 */
