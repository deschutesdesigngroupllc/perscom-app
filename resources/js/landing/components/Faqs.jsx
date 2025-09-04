import React from 'react'

import { Container } from './Container'
import backgroundImage from '../../../images/landing/background-faqs.jpg'

const faqs = [
  {
    question: 'What kind of customer support is available with PERSCOM.io?',
    answer:
      'We offer a range of customer support options, including email and forum support, as well as a comprehensive knowledge base and user guides. Our support team is available to help with any questions or issues you may encounter, and we are committed to providing prompt and reliable support to our customers.'
  },
  {
    question: 'Where does the name PERSCOM come from?',
    answer:
      'In the military, "PERSCOM" stands for "Personnel Command," which is a term used to describe a unit or department responsible for managing military personnel. However, it\'s worth noting that "PERSCOM.io" is not affiliated with any particular military organization or entity, and its name is simply derived from the term "personnel command."'
  },
  {
    question: 'Can I integrate PERSCOM.io with other tools and systems we use?',
    answer:
      "Yes, PERSCOM.io's API, Command Line Interface (CLI), OAuth 2.0, OpenID Connect (OIDC), Webhooks, and Widget integration options make it easy to connect with other tools and systems, allowing you to leverage the platform's powerful personnel management tools while still using the tools you're accustomed to."
  },
  {
    question: 'Can I change the URL of my account Dashboard?',
    answer: 'Yes. You must subscribe to the Pro plan and you can change it to whatever you would like.'
  },
  {
    question: 'Can I try PERSCOM.io before I purchase a subscription?',
    answer:
      'Absolutely. Visit the registration page and sign up for an account. You will have a 7-day feel trial before you need to purchase a subscription.'
  },
  {
    question: 'What payment methods do you support?',
    answer: 'We use Stripe as our payment gateway and support all major payment methods.'
  },
  {
    question: 'How secure is our data in PERSCOM.io?',
    answer:
      "PERSCOM.io is designed with security in mind and employs industry-standard security measures, including data encryption, role-based access control, and multi-factor authentication. Each organization is also its own tenant in a multi-tenant structure, ensuring that no organization's data is accessible by any other."
  },
  {
    question: 'What kind of organizations can benefit from PERSCOM.io?',
    answer:
      'PERSCOM.io is designed for paramilitary organizations, such as law enforcement agencies, military units, and emergency services organizations that utilize a chain of command or rigid organizational hierarchy.'
  },
  {
    question: 'Why should I purchase a PERSCOM.io subscription?',
    answer:
      "We understand the complexity of managing personnel. We've taken this into account and offer all the services you need in one product."
  }
]

export function Faqs() {
  return (
    <section id='faq' aria-labelledby='faq-title' className='relative overflow-hidden bg-gray-50 py-20 sm:py-32'>
      <div className='-trangray-x-[30%] -trangray-y-[25%] absolute top-0 left-1/2'>
        <img src={backgroundImage} alt='' width={1558} height={946} aria-hidden='true' />
      </div>
      <Container className='relative'>
        <div className='mx-auto max-w-2xl lg:mx-0'>
          <h2 className='text-3xl font-bold tracking-tight text-gray-800 sm:text-4xl'>Frequently asked questions</h2>
          <p className='mt-4 text-base'>
            If you can’t find what you’re looking for, visit our{' '}
            <a href='https://docs.perscom.io' target='_blank' rel='noreferrer' className='underline'>
              documentation
            </a>{' '}
            for more reference material.
          </p>
        </div>
        <div className='mx-auto mt-16 grid max-w-2xl grid-cols-1 gap-8 lg:max-w-none lg:grid-cols-3'>
          {faqs.map((faq, columnIndex) => (
            <div className='space-y-8' key={columnIndex}>
              <h3 className='text-base font-semibold'>{faq.question}</h3>
              <p className='mt-4 text-sm'>{faq.answer}</p>
            </div>
          ))}
        </div>
      </Container>
    </section>
  )
}
