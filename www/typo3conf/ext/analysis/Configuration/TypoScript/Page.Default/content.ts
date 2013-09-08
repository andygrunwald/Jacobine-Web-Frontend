# Col 0 (Normal column)
analysis.content.main < styles.content.get

# Col 1 (Left column)
analysis.content.main.left < styles.content.get
analysis.content.main.left.select.where = colPos=1

# Col 2 (Right column)
analysis.content.main.right < styles.content.get
analysis.content.main.right.select.where = colPos=2

# Col 3 (Border column)
analysis.content.main.border < styles.content.get
analysis.content.main.border.select.where = colPos=3
