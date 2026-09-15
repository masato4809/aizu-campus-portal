import * as React from 'react';
import { IPropsBase } from '@/script/System/System';
import { responsiveSpacing } from '@/script/System/Responsive';
import { EstablishedResult } from '@/script/Pages/ShuffleLunch/Established/EstabilshedResult';
import { PersonalResult } from '@/script/Pages/ShuffleLunch/Established/PersonalResult';

interface IProps extends IPropsBase {}
export const Established: React.FC<IProps> = () => {
  return (
    <>
      <PersonalResult sx={{ marginTop: responsiveSpacing(4) }} />
      <EstablishedResult sx={{ marginTop: responsiveSpacing(4) }} />
    </>
  );
};
