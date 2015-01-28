JSMin.php - modified PHP implementation of Douglas Crockford's JSMin.

<code>
$minifiedJs = \JSMin\Minify::minify($js);
</code>

This is a modified port of jsmin.c. Improvements:

Does not choke on some regexp literals containing quote characters. E.g. /'/

Spaces are preserved after some add/sub operators, so they are not mistakenly
converted to post-inc/dec. E.g. a + ++b -> a+ ++b

Preserves multi-line comments that begin with /*!
