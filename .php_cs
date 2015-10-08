<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in(__DIR__)
    ->exclude('views')
;

return Symfony\CS\Config\Config::create()
    ->fixers(array(
    	'-blankline_after_open_tag',
    	'-multiline_array_trailing_comma',
    	'-phpdoc_inline_tag',
    	'-phpdoc_no_empty_return',
    	'-phpdoc_short_description',
    	'-phpdoc_to_comment',
    	'-unalign_double_arrow',
    	'-unalign_equals',
    	'-unary_operators_spaces',
    	'align_equals',
    	'concat_with_spaces',
    	'short_array_syntax',
    ))
    ->finder($finder)
;
