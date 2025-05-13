//Interface för boenden
export interface Accommodation {
  id: string
  title: string
  emailaddress: string | null
  rent: string
  tenant: string
  real_estate_id: { target_id: string }[]
}
