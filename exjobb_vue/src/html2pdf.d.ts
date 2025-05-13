//Typdefinition för att hantera funtionerna i html2pdf.
declare module 'html2pdf.js' {
  export default function html2pdf(): {
    from: (element: HTMLElement) => {
      set: (options: any) => {
        save: () => Promise<void>
      }
    }
  }
}
