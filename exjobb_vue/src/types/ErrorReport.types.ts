//Interface för felrapporter
export interface ErrorReport {
  id?: string
  title: string
  message: string
  messagetype: string
  email: string
  name?: string
  accommodation_ref: string
  status?: string
  created?: Date
}
