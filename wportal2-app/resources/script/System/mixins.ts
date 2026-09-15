import { CSSProperties } from '@mui/material/styles/createMixins';

declare module '@mui/material/styles/createMixins' {
  // eslint-disable-next-line @typescript-eslint/naming-convention
  interface Mixins {
    drawer?: CSSProperties;
  }
}
