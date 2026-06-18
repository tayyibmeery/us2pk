export interface ColumnDefinition {
  key: string
  label: string
  sortable?: boolean          // default true
  format?: (value: any) => string  // optional formatter
}
