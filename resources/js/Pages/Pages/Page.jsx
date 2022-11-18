import React from "react";

import { Tenant } from "@/Layouts/Tenant"
import { Hero } from "@/Components/Hero";

export default function Page({ content }) {
  return (
      <Tenant>
        {content}
      </Tenant>
  );
}