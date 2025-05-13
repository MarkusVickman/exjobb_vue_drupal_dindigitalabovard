//Interface för information till boende
export interface Information {
  id: string
  realestate_id: { target_id: string }[]
  realestate_title: string
  title: string
  information: string
  created: Date
}
