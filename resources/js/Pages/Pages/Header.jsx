import React, {Fragment} from "react";
import {Popover, Transition} from "@headlessui/react";
import clsx from "clsx";

import {ButtonLink} from "../../Components/Button";
import {Container} from "../../Components/Container";
import {Logo} from "../../Components/Logo";

function MobileNavigation() {
    return (
        <Popover>
            {({ open, close }) => (
                <>
                    <Popover.Button className="relative z-10 flex h-8 w-8 items-center justify-center [&:not(:focus-visible)]:focus:outline-none">
                        <span className="sr-only">Toggle Navigation</span>
                        <svg
                            aria-hidden="true"
                            className="h-3.5 w-3.5 overflow-visible stroke-slate-700"
                            fill="none"
                            strokeWidth={2}
                            strokeLinecap="round"
                        >
                            <path
                                d="M0 1H14M0 7H14M0 13H14"
                                className={clsx("origin-center transition", {
                                    "scale-90 opacity-0": open,
                                })}
                            />
                            <path
                                d="M2 2L12 12M12 2L2 12"
                                className={clsx("origin-center transition", {
                                    "scale-90 opacity-0": !open,
                                })}
                            />
                        </svg>
                    </Popover.Button>
                    <Transition.Root>
                        <Transition.Child
                            as={Fragment}
                            enter="duration-150 ease-out"
                            enterFrom="opacity-0"
                            enterTo="opacity-100"
                            leave="duration-150 ease-in"
                            leaveFrom="opacity-100"
                            leaveTo="opacity-0"
                        >
                            <Popover.Overlay className="fixed inset-0 bg-slate-300/50" />
                        </Transition.Child>
                        <Transition.Child
                            as={Fragment}
                            enter="duration-150 ease-out"
                            enterFrom="opacity-0 scale-95"
                            enterTo="opacity-100 scale-100"
                            leave="duration-100 ease-in"
                            leaveFrom="opacity-100 scale-100"
                            leaveTo="opacity-0 scale-95"
                        >
                            <Popover.Panel
                                as="ul"
                                className="absolute inset-x-0 top-full mt-4 origin-top space-y-4 rounded-2xl bg-white p-6 text-lg tracking-tight text-slate-900 shadow-xl ring-1 ring-slate-900/5"
                            ></Popover.Panel>
                        </Transition.Child>
                    </Transition.Root>
                </>
            )}
        </Popover>
    );
}

export function Header() {
    return (
        <header className="py-4">
            <Container>
                <nav className="relative z-50 text-sm">
                    <ul className="flex items-center">
                        <li>
                            <a href={route("landing.home")}>
                                <span className="sr-only">Home</span>
                                <Logo className="h-8 w-auto sm:h-10 md:h-12" />
                            </a>
                        </li>
                        <li className="ml-auto">
                            <ButtonLink href={route("nova.pages.dashboard")} color="blue">
                                <span>
                                    <span className="hidden lg:inline">Go to </span>
                                    Dashboard
                                </span>
                            </ButtonLink>
                        </li>
                        <li className="ml-5 -mr-1 md:hidden">
                            <MobileNavigation />
                        </li>
                    </ul>
                </nav>
            </Container>
        </header>
    );
}
