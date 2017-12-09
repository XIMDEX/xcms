try:
    import Products.CMFPlone
except ImportError:
    raise RuntimeError('These tests depend on CMFPlone.')
