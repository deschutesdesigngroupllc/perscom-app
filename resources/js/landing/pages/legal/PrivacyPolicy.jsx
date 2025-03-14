import React from 'react'
import { Header } from '@/landing/components/Header'
import { Footer } from '@/landing/components/Footer'
import Container from '@/landing/components/Container'
import GetTermsEmbed from '@/landing/components/GetTermsEmbed.jsx'
import LegalPolicySelector from '@/landing/components/LegalPolicySelector.jsx'
function PrivacyPolicy() {
  return (
    <>
      <Header />
      <Container className='prose pb-16 pt-4 lg:pt-8'>
        <LegalPolicySelector />
        <GetTermsEmbed policyId='fXqwd' policyName='privacy' />
      </Container>
      <Footer />
    </>
  )
}

export default PrivacyPolicy
