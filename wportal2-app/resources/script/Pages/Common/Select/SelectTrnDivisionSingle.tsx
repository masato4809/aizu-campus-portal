import * as React from 'react';
import { Autocomplete, Box, SxProps, TextField, Theme } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { Loading } from '@/script/Component/Misc/Loading';
import { useFetchAppTrnDivisionList } from '@/script/Hooks/App/Queries/useFetchAppTrnDivisionList';
import { E_COLOR } from '@/script/Enum/EColor';
import { InputError } from '@/script/Component/Form/InputError';
import { Closable } from '@/script/Component/Misc/Closable';

// 表示するオプション
interface IOptionHash {
  [id: number]: string;
}

interface IProps extends IPropsBase {
  value: number;
  onChange: (value: number) => void;
  visibleError?: boolean;
  errors?: string[];
  sxSelect?: SxProps<Theme>;
}
export const SelectTrnDivisionSingle: React.FC<IProps> = ({
  sx,
  value,
  onChange,
  visibleError,
  errors,
  sxSelect,
}) => {
  const [loadingTrnDivision, trnDivisionList] = useFetchAppTrnDivisionList();
  const loading = loadingTrnDivision;
  const [isEdited, setIsEdited] = React.useState<boolean>(false);

  /**
   * ロード時に編集フラグを落とす.
   */
  React.useEffect(() => {
    setIsEdited(false);
  }, [errors]);

  /**
   * 背景色の取得.
   */
  const getBackgroundColor = (): string => {
    if (errors === undefined || !errors.length) {
      return E_COLOR.WHITE;
    }
    return !isEdited ? E_COLOR.RED_LIGHT : E_COLOR.WHITE;
  };

  // 表示対象.
  const options: number[] = trnDivisionList.list().map(v => v.id);

  // メモ化した表示情報.
  const optionHashes: IOptionHash = React.useMemo((): IOptionHash => {
    return Object.fromEntries(
      trnDivisionList.list().map(division => {
        return [division.id, `[${division.id}]${division.name}`];
      }),
    );
  }, [options.length]);

  const getOptionLabel = (option: number): string => {
    if (!option) {
      return '';
    }
    return optionHashes[option] as string;
  };

  const handleChange = (value: number) => {
    setIsEdited(true);
    onChange(value);
  };

  return (
    <Box sx={{ ...sx }}>
      <Loading loading={loading}>
        <Autocomplete<number>
          value={value}
          options={options}
          getOptionLabel={option => getOptionLabel(option)}
          getOptionKey={option => String(option)}
          onChange={(e, value) => handleChange(Number(value))}
          renderInput={params => (
            <TextField
              {...params}
              sx={{
                backgroundColor: getBackgroundColor(),
                ...sxSelect,
              }}
              placeholder="ID、名前で絞り込み"
            />
          )}
        />
      </Loading>
      <Closable open={visibleError}>
        <InputError sx={{ marginLeft: '0' }} errors={errors} />
      </Closable>
    </Box>
  );
};
