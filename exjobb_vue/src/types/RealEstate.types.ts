//Interface för fastigheter
export interface RealEstate {
  id: string
  title: string
  autoinvoice: boolean
  invoice_due_date: number
  invoice_send_date: number
  streetaddress: string
  payment_method: string
  payment_number: string
}

//Interface för fastighetslista. används på TenantView.vue
export interface RealEstateId {
  id: string
  title: string
}
