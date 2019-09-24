### Version 2.2.5 - October, 27, 2016

- **Progress** - Progress now includes transitionEnd failback for progress bar animations, this will prevent labels from continuing to be updated if the `transitionEnd` css callback does not fire correctly
- **Transition** - You can now specify `data-display` to specify the final display state for an animation in cases that it is detected incorrectly (you can also pass in as a setting)