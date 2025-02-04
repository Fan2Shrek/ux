/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import type { ComponentClass, FunctionComponent } from 'react';
import { type ComponentCollection, components } from './components.js';

type Component = string | FunctionComponent<object> | ComponentClass<object, any>;

declare global {
    function resolveReactComponent(name: string): Component;

    interface Window {
        resolveReactComponent(name: string): Component;
    }
}

export function registerReactControllerComponents(reactComponents: ComponentCollection = components): void {
    // Expose a global React loader to allow rendering from the Stimulus controller
    window.resolveReactComponent = (name: string): Component => {
        const component = reactComponents[name];
        if (typeof component === 'undefined') {
            const possibleValues: string =
                Object.keys(reactComponents).length > 0 ? Object.keys(reactComponents).join(', ') : 'none';

            throw new Error(`React controller "${name}" does not exist. Possible values: ${possibleValues}`);
        }

        return component;
    };
}
