# make this dir a package, and allow access for Zope 2 'untrusted' code

def __allow_access_to_unprotected_subobjects__(name, value=None):
    return name in ('version_management')
