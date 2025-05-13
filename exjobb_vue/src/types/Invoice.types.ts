//Interface för fakturor
export interface Invoice {
  id: string
  invoice_number: number
  tenant_name: string
  title: string
  invoice_html: string
  status: string
  created: Date
  email: string
  accommodationId: { target_id: string }[]
}
