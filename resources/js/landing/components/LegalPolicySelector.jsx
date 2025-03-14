import React from 'react'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select.jsx'
import Label from '@/landing/components/Label.jsx'
import { router } from '@inertiajs/react'

export function LegalPolicySelector() {
  const handleChange = (value) => {
    router.visit(value, {
      replace: true
    })
  }

  return (
    <Select onValueChange={handleChange}>
      <SelectTrigger className='w-full sm:w-[180px]'>
        <SelectValue placeholder='Policies' />
      </SelectTrigger>
      <SelectContent>
        <SelectItem value={route('web.acceptable-use-policy')}>Acceptable Use Policy</SelectItem>
        <SelectItem value={route('web.cookie-policy')}>Cookie Policy</SelectItem>
        <SelectItem value={route('web.privacy-policy')}>Privacy Policy</SelectItem>
        <SelectItem value={route('web.terms-of-service')}>Terms of Service</SelectItem>
      </SelectContent>
    </Select>
  )
}

export default LegalPolicySelector
