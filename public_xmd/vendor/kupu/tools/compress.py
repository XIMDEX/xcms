#!/usr/bin/env python

"""Remove comments, newlines and redundant whitespace from JavaScript code

    This reads all paths that were passed in as arguments on the command-line
    and removes everything that is ignored by JavaScript. This makes that
    the source isn't readable anymore (which I personally consider bad),
    but also that less bytes have to be served by the server, scripts are
    loaded faster and also that they're executed faster.
    
    WARNING: This script converts files in place! Original files will be
    overwritten. Do *not* run this on a development version of your code,
    since you won't be able to get them back into the original state. This
    should be ran only by system administrators if they want to speed up
    their setups.
"""

import sys, re

one_line_comment = re.compile(r'^\s*//.*$', re.M)
trailing_comment = re.compile(r'//(\w|\s)*$', re.M)
multi_line_comment = re.compile(r'^\s*/\*.*?\*/', re.M | re.S)
whitespace_after_separator = re.compile(r';\s*', re.M | re.S)
whitespace_after_opening_bracket = re.compile(r'{\s*', re.M | re.S)
starting_whitespace = re.compile(r'^\s*', re.M | re.S)

def strip(data):
    """Processes the data, removing comments and unecessary whitespace."""
    data = one_line_comment.sub('', data)
    data = trailing_comment.sub('', data)
    data = multi_line_comment.sub('', data)
    data = whitespace_after_separator.sub(';', data)
    data = whitespace_after_opening_bracket.sub('{', data)
    data = starting_whitespace.sub('', data)
    return data.strip()

for file in sys.argv[1:]:
    data = open(file).read()
    data = strip(data)
    open(file, 'w').write(data)
