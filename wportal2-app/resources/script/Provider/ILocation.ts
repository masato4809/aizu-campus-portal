export interface ILocation {
  current: string;
  path: string;
}

/**
 * ILocationの初期値.
 */
export const getDefaultLocation = (): ILocation => {
  return {
    current: '',
    path: '',
  };
};

// eslint-disable-next-line @typescript-eslint/no-explicit-any
export const parseLocation = (location: any): ILocation => {
  return {
    current: location?.current ? String(location.current) : '',
    path: location?.path ? String(location.path) : '',
  };
};
